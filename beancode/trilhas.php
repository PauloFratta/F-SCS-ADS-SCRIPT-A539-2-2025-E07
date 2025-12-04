<?php
// Inicia a sessão para manter o estado do usuário e da trilha
session_start();
include 'db.php';

// Verifica se o usuário está logado como aluno
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: index.php?error=Você precisa estar logado como aluno");
    exit();
}

// ==========================================================
// LÓGICA PHP DE AUTENTICAÇÃO E ESTRUTURA DE DADOS
// ==========================================================

// Busca informações do aluno no banco de dados
$aluno_id = $_SESSION['user_id'];
$sql = "SELECT nome_user, trilha_ativa FROM alunos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $aluno_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $aluno = $result->fetch_assoc();
    $user_name = $aluno['nome_user'];
    $user_track = $aluno['trilha_ativa']; // Pode ser NULL no primeiro login
} else {
    $stmt->close();
    close_db_connection();
    header("Location: index.php?error=Aluno não encontrado");
    exit();
}
$stmt->close();

// ==========================================================
// BUSCA DINÂMICA DE TRILHAS E PROGRESSO DO BANCO
// ==========================================================

// Busca todos os caminhos (trilhas) e seus cursos
$sql_trilhas = "SELECT cam.id, cam.nome, cam.descricao, 
                c.id as curso_id, c.nome_curso,
                LOWER(CASE 
                    WHEN c.nome_curso LIKE '%Iniciante%' THEN 'iniciante'
                    WHEN c.nome_curso LIKE '%Intermediário%' THEN 'intermediario'
                    WHEN c.nome_curso LIKE '%Avançado%' THEN 'avancado'
                    ELSE 'iniciante'
                END) as slug
                FROM caminhos cam
                LEFT JOIN cursos c ON c.caminho_id = cam.id
                ORDER BY cam.id";
$result_trilhas = $conn->query($sql_trilhas);

$tracks = [];
while ($trilha = $result_trilhas->fetch_assoc()) {
    $slug = $trilha['slug'];
    
    // Define cores e estilos por trilha
    $styles = [
        'iniciante' => ['emoji' => '🌟', 'level' => 'Iniciante', 'bg' => 'bg-purple-100', 'border' => 'border-purple-200', 'color' => 'text-primary'],
        'intermediario' => ['emoji' => '🎮', 'level' => 'Intermediário', 'bg' => 'bg-orange-100', 'border' => 'border-orange-200', 'color' => 'text-secondary'],
        'avancado' => ['emoji' => '🧙‍♂️', 'level' => 'Avançado', 'bg' => 'bg-teal-100', 'border' => 'border-teal-200',
        'color' => 'text-teal-600']
    ];
    
    $style = $styles[$slug] ?? $styles['iniciante'];
    
    // Busca módulos e lições desta trilha
    $modules = [];
    if ($trilha['curso_id']) {
        $sql_modulos = "SELECT m.id, m.nome, m.descricao, m.ordem,
                        (SELECT COUNT(*) FROM licoes WHERE modulo_id = m.id) as total_licoes,
                        (SELECT COUNT(*) FROM licoes l 
                         INNER JOIN progresso_licoes pl ON l.id = pl.licao_id 
                         WHERE l.modulo_id = m.id AND pl.aluno_id = ? AND pl.concluida = 1) as licoes_concluidas
                        FROM modulos m
                        WHERE m.curso_id = ?
                        ORDER BY m.ordem
                        LIMIT 3";
        $stmt_mod = $conn->prepare($sql_modulos);
        $stmt_mod->bind_param("ii", $aluno_id, $trilha['curso_id']);
        $stmt_mod->execute();
        $result_mod = $stmt_mod->get_result();
        
        while ($modulo = $result_mod->fetch_assoc()) {
            // Busca lições do módulo
            $sql_licoes = "SELECT l.id, l.titulo, 
                           COALESCE(pl.concluida, 0) as concluida
                           FROM licoes l
                           LEFT JOIN progresso_licoes pl ON l.id = pl.licao_id AND pl.aluno_id = ?
                           WHERE l.modulo_id = ?
                           ORDER BY l.ordem
                           LIMIT 3";
            $stmt_lic = $conn->prepare($sql_licoes);
            $stmt_lic->bind_param("ii", $aluno_id, $modulo['id']);
            $stmt_lic->execute();
            $result_lic = $stmt_lic->get_result();
            
            $lessons = [];
            while ($licao = $result_lic->fetch_assoc()) {
                $lessons[] = [
                    'id' => $licao['id'],
                    'title' => $licao['titulo'],
                    'status' => $licao['concluida'] ? 'Concluído' : 'Pendente'
                ];
            }
            $stmt_lic->close();
            
            $modules[] = [
                'title' => $modulo['nome'],
                'lessons' => $lessons,
                'progress' => $modulo['total_licoes'] > 0 ? 
                    round(($modulo['licoes_concluidas'] / $modulo['total_licoes']) * 100) : 0
            ];
        }
        $stmt_mod->close();
    }
    
    $tracks[$slug] = [
        'title' => $trilha['nome'],
        'subtitle' => $trilha['descricao'],
        'emoji' => $style['emoji'],
        'level' => $style['level'],
        'bg' => $style['bg'],
        'border' => $style['border'],
        'color' => $style['color'],
        'modules' => $modules
    ];
}

close_db_connection();

// Lógica para determinar a trilha ativa e se o modal deve abrir:
// Se trilha_ativa é NULL ou vazio, é o primeiro login
$has_selected_track = !empty($user_track);
$active_track_key = $has_selected_track ? $user_track : 'iniciante'; 
$active_track_data = $tracks[$active_track_key] ?? $tracks['iniciante'];

// Calcula o progresso geral da trilha ativa
$total_licoes_trilha = 0;
$licoes_concluidas_trilha = 0;
foreach ($active_track_data['modules'] as $module) {
    $total_licoes_trilha += count($module['lessons']);
    foreach ($module['lessons'] as $lesson) {
        if ($lesson['status'] === 'Concluído') {
            $licoes_concluidas_trilha++;
        }
    }
}
$progresso_percentual = $total_licoes_trilha > 0 ? round(($licoes_concluidas_trilha / $total_licoes_trilha) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BeanCode - Suas Trilhas de Curso</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Mantendo a coerência de estilo do index.php */
    :root {
      --background: oklch(0.98 0.02 280);
      --foreground: oklch(0.15 0.05 260);
      --card: oklch(1 0 0);
      --card-foreground: oklch(0.15 0.05 260);
      --primary: oklch(0.55 0.15 280);
      --primary-foreground: oklch(0.98 0 0);
      --secondary: oklch(0.75 0.12 45);
      --secondary-foreground: oklch(0.98 0 0);
      --muted: oklch(0.95 0.02 280);
      --muted-foreground: oklch(0.45 0.05 260);
      --accent: oklch(0.65 0.18 160);
      --accent-foreground: oklch(0.98 0 0);
      --border: oklch(0.9 0.02 280);
    }

    body {
      background-color: var(--background);
      color: var(--foreground);
      font-family: system-ui, -apple-system, sans-serif;
    }
    
    .bg-primary { background-color: var(--primary); }
    .bg-secondary { background-color: var(--secondary); }
    .bg-card { background-color: var(--card); }
    .text-primary { color: var(--primary); }
    .text-secondary { color: var(--secondary); }
    .text-accent { color: var(--accent); }
    .text-foreground { color: var(--foreground); }
    .border-border { border-color: var(--border); }

    /* Estilo para o Modal (Pop-up) */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: none; /* Inicia oculto */
        justify-content: center;
        align-items: center;
        z-index: 60;
    }
    .modal-content {
        background-color: var(--card);
        border-radius: 0.75rem;
        padding: 1.5rem;
        width: 90%;
        max-width: 600px; /* Maior para caber as opções de trilha */
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        transform: scale(0.95);
        opacity: 0;
        transition: all 0.3s ease-out;
    }
    .modal-overlay.open .modal-content {
        transform: scale(1);
        opacity: 1;
    }
    .modal-overlay.open {
        display: flex;
    }

    /* Estilos para cards de trilha */
    .track-card {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    .track-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .track-card.selected {
        border-width: 3px;
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(88, 51, 153, 0.3); /* Cor primária */
    }

    /* Estilo para Lições */
    .lesson-item {
        transition: background-color 0.2s;
    }
    .lesson-item:hover {
        background-color: var(--muted);
    }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <header class="sticky top-0 z-50 w-full border-b backdrop-blur bg-white/95 border-border">
    <div class="container mx-auto flex h-16 items-center justify-between px-4">
      <div class="flex items-center space-x-2">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary">
          <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/>
          </svg>
        </div>
        <span class="text-xl font-bold text-foreground">BeanCode</span>
      </div>

      <div class="flex items-center space-x-4">
        <span class="text-sm font-medium text-gray-600">Olá, <?php echo htmlspecialchars($user_name); ?>!</span>
        <a href="logout.php" class="px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-100 transition-colors">Sair</a>
        <button onclick="openModal('trackSelectionModal')" class="px-4 py-2 text-sm font-medium rounded-lg bg-secondary text-white hover:opacity-90 transition-opacity">Mudar Trilha</button>
      </div>
    </div>
  </header>

  <main class="flex-grow py-12 lg:py-20 bg-purple-50">
    <div class="container mx-auto px-4 max-w-5xl">
      <div class="text-center mb-10">
        <h1 class="text-4xl font-bold tracking-tight mb-2">Seu Caminho de Programação</h1>
        <p class="text-lg text-gray-600">Sua aventura de hoje é na trilha **<?php echo htmlspecialchars($active_track_data['title']); ?>**.</p>
      </div>

      <section id="active-track" class="mb-12">
        
        <div class="bg-card p-6 md:p-10 rounded-xl shadow-2xl border-4 border-primary/50 flex flex-col items-center gap-6">
          <div class="flex flex-col md:flex-row items-center w-full gap-6">
              <div class="text-6xl md:text-8xl"><?php echo $active_track_data['emoji']; ?></div>
              <div class="flex-grow text-center md:text-left">
                <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $active_track_data['bg'] . ' ' . $active_track_data['color'] . ' border ' . $active_track_data['border']; ?>">
                  Trilha Ativa
                </span>
                <h2 class="text-3xl font-bold mt-2 mb-1"><?php echo $active_track_data['title']; ?></h2>
                <p class="text-gray-600 mb-4"><?php echo $active_track_data['subtitle']; ?></p>
                
                <div class="flex items-center justify-center md:justify-start gap-3">
                    <div class="w-full max-w-xs bg-gray-200 rounded-full h-2.5">
                        <div class="bg-primary h-2.5 rounded-full transition-all duration-500" style="width: <?php echo $progresso_percentual; ?>%"></div>
                    </div>
                    <span class="text-sm font-semibold text-primary"><?php echo $progresso_percentual; ?>% Concluído</span>
                </div>
              </div>
              <?php
                // Encontra a primeira lição não concluída
                $primeira_licao_pendente = null;
                foreach ($active_track_data['modules'] as $module) {
                    foreach ($module['lessons'] as $lesson) {
                        if ($lesson['status'] === 'Pendente') {
                            $primeira_licao_pendente = $lesson['id'];
                            break 2; // Sai dos dois loops
                        }
                    }
                }
                
                if ($primeira_licao_pendente):
              ?>
              <a href="<?php echo $active_track_key; ?>.php?licao_id=<?php echo $primeira_licao_pendente; ?>" class="w-full md:w-auto bg-primary text-white hover:opacity-90 px-8 py-3 text-lg font-semibold rounded-lg shadow-lg transition-all duration-300 flex items-center justify-center gap-2">
                Continuar o Curso
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
              </a>
              <?php else: ?>
              <div class="w-full md:w-auto bg-green-600 text-white px-8 py-3 text-lg font-semibold rounded-lg shadow-lg flex items-center justify-center gap-2">
                ✅ Trilha Concluída!
              </div>
              <?php endif; ?>
          </div>
          
          <hr class="w-full border-gray-200 my-4">

          <div class="w-full space-y-6">
            <h3 class="text-2xl font-bold text-foreground">Conteúdo da Trilha</h3>
            
            <?php foreach ($active_track_data['modules'] as $module): ?>
                <div class="bg-muted p-4 rounded-lg border border-border">
                    <h4 class="text-xl font-semibold mb-3 text-primary"><?php echo htmlspecialchars($module['title']); ?></h4>
                    
                    <ul class="space-y-2">
                        <?php foreach ($module['lessons'] as $lesson): ?>
                            <li class="flex justify-between items-center p-3 rounded-lg lesson-item bg-white">
                                <span class="text-gray-700">
                                    <?php echo htmlspecialchars($lesson['title']); ?>
                                </span>
                                <?php if ($lesson['status'] === 'Concluído'): ?>
                                    <span class="text-sm font-medium text-green-600 flex items-center gap-1">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Concluído
                                    </span>
                                <?php else: ?>
                                    <a href="<?php echo $active_track_key; ?>.php?licao_id=<?php echo $lesson['id']; ?>" class="text-sm font-medium text-primary hover:underline">
                                        Começar Lição
                                    </a>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <section id="all-tracks">
        <h3 class="text-2xl font-bold mb-6 text-center">Outras Trilhas Disponíveis</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <?php foreach ($tracks as $key => $t): ?>
            <div class="group hover:shadow-xl transition-all duration-300 border border-gray-200 rounded-xl overflow-hidden bg-white p-6 text-center h-full">
              <div class="text-4xl mb-4"><?php echo $t['emoji']; ?></div>
              <span class="px-3 py-1 rounded-full text-xs font-medium <?php echo $t['bg'] . ' ' . $t['color'] . ' border ' . $t['border']; ?>">
                <?php echo $t['level']; ?>
              </span>
              <h4 class="text-xl font-semibold mt-2 mb-2 group-hover:text-primary transition-colors"><?php echo $t['title']; ?></h4>
              <p class="text-gray-600 text-sm mb-4"><?php echo $t['subtitle']; ?></p>

              <?php if ($key === $active_track_key): ?>
                <button disabled class="w-full bg-gray-300 text-gray-700 py-2 rounded-lg font-semibold cursor-not-allowed">
                  Trilha Atual
                </button>
              <?php else: ?>
                <button onclick="openModal('trackSelectionModal')" class="w-full bg-accent text-white hover:opacity-90 py-2 rounded-lg font-semibold transition-all duration-300">
                  Trocar para esta Trilha
                </button>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
      
    </div>
  </main>

  <footer class="border-t border-gray-200 bg-white">
    <div class="container mx-auto px-4 py-6 text-center">
      <p class="text-sm text-gray-600">© 2025 BeanCode. Feito com ❤️ para jovens programadores.</p>
    </div>
  </footer>


  <div id="trackSelectionModal" class="modal-overlay">
    <div class="modal-content">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary">Escolha Sua Aventura!</h2>
        <?php if ($has_selected_track): // Permite fechar apenas se já escolheu uma vez ?>
            <button onclick="closeModal('trackSelectionModal')" class="text-gray-400 hover:text-gray-600">
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        <?php endif; ?>
      </div>

      <p class="text-gray-700 mb-6">Selecione a trilha que mais combina com seu nível. Sua trilha ativa é <span class="font-semibold"><?php echo htmlspecialchars($active_track_data['title']); ?></span>.</p>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        
        <?php foreach ($tracks as $key => $t): ?>
          <div 
            id="track-card-<?php echo $key; ?>"
            class="track-card border-2 border-transparent rounded-xl p-4 text-center <?php echo $t['bg'] . ($key === $active_track_key ? ' selected' : ''); ?>"
            onclick="selectTrack('<?php echo $key; ?>')"
          >
            <div class="text-4xl mb-2"><?php echo $t['emoji']; ?></div>
            <h4 class="text-lg font-semibold <?php echo $t['color']; ?>"><?php echo $t['title']; ?></h4>
            <p class="text-xs text-gray-600 mt-1"><?php echo $t['level']; ?></p>
          </div>
        <?php endforeach; ?>

      </div>

      <div class="mt-8">
        <button id="selectTrackButton" disabled class="w-full bg-primary text-white py-3 rounded-lg font-semibold opacity-50 cursor-not-allowed transition-opacity hover:opacity-100" onclick="confirmTrackSelection()">
          Confirmar Seleção de Trilha
        </button>
      </div>
      
      <form id="trackForm" method="POST" action="courses.php" class="hidden">
          <input type="hidden" name="selected_track" id="selectedTrackInput">
      </form>

    </div>
  </div>


  <script>
    let selectedTrack = '<?php echo $active_track_key; ?>'; // Inicia com a trilha ativa
    const hasSelectedTrack = <?php echo $has_selected_track ? 'true' : 'false'; ?>;

    // --- Funções de Modal ---
    function openModal(modalId) {
      document.getElementById(modalId).classList.add('open');
      document.body.style.overflow = 'hidden'; 
      // Reseta o estado do botão ao abrir
      const selectButton = document.getElementById('selectTrackButton');
      selectButton.disabled = true;
      selectButton.classList.add('opacity-50', 'cursor-not-allowed');
      selectButton.classList.remove('hover:opacity-100');
      
      // Garante que o cartão ativo esteja selecionado visualmente
      selectTrack('<?php echo $active_track_key; ?>'); 
    }

    function closeModal(modalId) {
      document.getElementById(modalId).classList.remove('open');
      document.body.style.overflow = ''; 
    }
    
    // --- Funções de Seleção de Trilha ---

    /**
     * Marca o cartão da trilha como selecionado visualmente.
     * @param {string} trackKey - A chave da trilha (ex: 'iniciante').
     */
    function selectTrack(trackKey) {
      selectedTrack = trackKey;
      const cards = document.querySelectorAll('.track-card');
      const selectButton = document.getElementById('selectTrackButton');

      cards.forEach(card => card.classList.remove('selected'));
      document.getElementById(`track-card-${trackKey}`).classList.add('selected');

      // Habilita o botão se a trilha for diferente da ativa (ou se for o primeiro acesso)
      if (selectedTrack !== '<?php echo $active_track_key; ?>' || !hasSelectedTrack) {
        selectButton.disabled = false;
        selectButton.classList.remove('opacity-50', 'cursor-not-allowed');
        selectButton.classList.add('hover:opacity-100');
      } else {
        selectButton.disabled = true;
        selectButton.classList.add('opacity-50', 'cursor-not-allowed');
        selectButton.classList.remove('hover:opacity-100');
      }
    }

    /**
     * Submete a trilha selecionada via formulário POST oculto.
     */
    function confirmTrackSelection() {
      if (selectedTrack) {
        // Define o valor do input oculto e submete
        document.getElementById('selectedTrackInput').value = selectedTrack;
        document.getElementById('trackForm').submit();
      }
    }

    // --- Abertura Automática do Modal ---
    window.onload = function() {
      // Abre o modal automaticamente se o usuário nunca selecionou uma trilha
      if (!hasSelectedTrack) {
        openModal('trackSelectionModal');
      }
    };
  </script>
</body>
</html>