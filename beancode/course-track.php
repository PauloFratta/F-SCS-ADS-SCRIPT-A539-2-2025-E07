<?php
// course-track.php (REVISADO PARA COERÊNCIA COM O SQL)

session_start();
// 1. OBRIGATÓRIO: Inclui o arquivo de conexão com o banco de dados
include 'db.php'; 

// Redireciona se o usuário não estiver logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$responsible_id = $_SESSION['user_id'] ?? null;
$user_type = $_SESSION['user_type'] ?? null;
$user_id_to_view = null;
$user_name = "Aluno";
$course_slug = 'iniciante'; // Padrão

// =========================================================
// LÓGICA DE MONITORAMENTO DO RESPONSÁVEL
// =========================================================

if ($user_type === 'responsible' && isset($_GET['aluno_id'])) {
    $aluno_id = (int)$_GET['aluno_id'];
    
    // 2. VERIFICAÇÃO DE SEGURANÇA: Garante que este aluno pertence ao responsável logado
    $sql_check = "SELECT id, nome_user, trilha_ativa FROM alunos WHERE id = ? AND responsavel_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $aluno_id, $responsible_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    
    if ($result_check->num_rows === 0) {
        // Aluno não encontrado ou não autorizado
        header("Location: dashboard_responsavel.php?error=acesso_negado");
        exit();
    }
    
    $aluno_data = $result_check->fetch_assoc();
    $user_id_to_view = $aluno_data['id'];
    $user_name = $aluno_data['nome_user'];
    $course_slug = $aluno_data['trilha_ativa'];
    $monitoring_mode = true;
    $view_title = "Monitorando: " . htmlspecialchars($user_name);
    
} elseif ($user_type === 'child') {
    // Lógica para o próprio aluno logado (se você implementar)
    $user_id_to_view = $_SESSION['user_id'];
    // Você precisaria de um campo 'nome_aluno' na sessão e 'trilha_ativa' no BD
    $user_name = $_SESSION['nome_aluno'] ?? 'Meu Perfil';
    $monitoring_mode = false;
    $view_title = "Minha Trilha de Cursos";

} else {
    // Redireciona se não for responsável com ID válido nem aluno logado
    header("Location: login.php");
    exit();
}

// =========================================================
// BUSCA DE PROGRESSO
// =========================================================

$progresso = [];
// CORREÇÃO: Usando a tabela 'progresso_licoes' e a coluna 'aluno_id'
$sql = "SELECT modulo_id, status FROM progresso_licoes WHERE aluno_id = ? AND curso_slug = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id_to_view, $course_slug);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $progresso[$row['modulo_id']] = $row['status'];
}
$stmt->close();
$conn->close();

// Definição estática dos módulos (idealmente viria de uma tabela 'modulos' do BD)
$modulos = [
    1 => ['titulo' => 'Primeiros Passos (Iniciante)', 'descricao' => 'Blocos, Sequências e Loops.', 'emoji' => '🌟', 'status_key' => 'iniciante'],
    2 => ['titulo' => 'O Criador de Jogos (Intermediário)', 'descricao' => 'Variáveis, Condicionais (If/Else) e Colisões.', 'emoji' => '🎮', 'status_key' => 'intermediario'],
    3 => ['titulo' => 'Mago da Web (Avançado)', 'descricao' => 'Introdução ao HTML e CSS.', 'emoji' => '🧙‍♂️', 'status_key' => 'avancado'],
];

// Define as cores e título com base na trilha ATIVA do aluno
$track_info = [
    'iniciante' => ['title' => 'Primeiros Passos', 'emoji' => '🌟', 'color' => 'text-primary', 'bg' => 'bg-purple-100'],
    'intermediario' => ['title' => 'Criador de Jogos', 'emoji' => '🎮', 'color' => 'text-secondary', 'bg' => 'bg-orange-100'],
    'avancado' => ['title' => 'Mago da Web', 'emoji' => '🧙‍♂️', 'color' => 'text-teal-600', 'bg' => 'bg-teal-100'],
];

$current_track = $track_info[$course_slug] ?? $track_info['iniciante']; // Pega o info da trilha ativa

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BeanCode - <?php echo htmlspecialchars($current_track['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* (Estilos base do projeto) */
        :root {
            --background: oklch(0.98 0.02 280); --foreground: oklch(0.15 0.05 260);
            --card: oklch(1 0 0); --primary: oklch(0.55 0.15 280);
            --secondary: oklch(0.75 0.12 45); --border: oklch(0.9 0.02 280);
        }
        body { background-color: var(--background); color: var(--foreground); }
        .bg-primary { background-color: var(--primary); }
        .bg-secondary { background-color: var(--secondary); }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <header class="sticky top-0 z-50 w-full border-b backdrop-blur bg-white/95 border-border">
        <div class="container mx-auto flex h-16 items-center justify-between px-4 max-w-6xl">
            <div class="flex items-center space-x-2">
                <span class="text-xl font-bold text-foreground">BeanCode | <?php echo htmlspecialchars($current_track['title']); ?></span>
            </div>
            <div class="flex items-center space-x-4">
                <?php if ($monitoring_mode): ?>
                    <a href="dashboard_responsavel.php" class="text-sm font-medium text-gray-600 hover:text-primary">Voltar ao Painel</a>
                <?php endif; ?>
                <a href="logout.php" class="px-4 py-2 text-sm font-medium rounded-lg bg-secondary text-white hover:opacity-90 transition-opacity">Sair</a>
            </div>
        </div>
    </header>

    <main class="flex-grow py-12 lg:py-20">
        <div class="container mx-auto px-4 max-w-4xl">
            <h1 class="text-4xl font-extrabold text-center mb-4 <?php echo $current_track['color']; ?>">
                <?php echo $current_track['emoji']; ?> Trilha: <?php echo htmlspecialchars($current_track['title']); ?>
            </h1>
            <?php if ($monitoring_mode): ?>
                <p class="text-xl text-center text-gray-600 mb-10">Monitorando o progresso de: <span class="font-bold text-foreground"><?php echo htmlspecialchars($user_name); ?></span></p>
            <?php else: ?>
                <p class="text-xl text-center text-gray-600 mb-10">Hora de Codar, <?php echo htmlspecialchars($user_name); ?>!</p>
            <?php endif; ?>

            <div class="space-y-6">
                
                <?php foreach ($modulos as $id => $modulo): 
                    $status_key = $modulo['status_key'];
                    // Assume que está pendente se não estiver no array $progresso
                    $status = $progresso[$id] ?? 'pendente'; 
                    
                    $is_completed = ($status === 'concluido');
                    $is_current = ($status === 'em_progresso');

                    // Define cores com base no status
                    $bg_color = $is_completed ? 'bg-primary' : ($is_current ? 'bg-secondary' : 'bg-gray-400');
                    $text_color = $is_completed ? 'text-primary' : ($is_current ? 'text-secondary' : 'text-gray-700');
                ?>
                <div class="flex items-start bg-white p-6 rounded-xl shadow-lg border border-gray-200 transition-all duration-300 hover:shadow-xl">
                    <div class="flex-shrink-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-xl font-bold text-white <?php echo $bg_color; ?> shadow-md">
                            <?php if ($is_completed): ?>
                                <span class="text-white">✓</span>
                            <?php elseif ($is_current): ?>
                                <span class="text-white">...</span>
                            <?php else: ?>
                                <span class="text-white"><?php echo $id; ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="flex-grow pt-1 ml-4">
                        <h3 class="text-xl font-bold <?php echo $text_color; ?>">Módulo <?php echo $id; ?>: <?php echo htmlspecialchars($modulo['titulo']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($modulo['descricao']); ?></p>

                        <?php if ($is_completed): ?>
                            <div class="text-sm text-primary font-semibold flex items-center gap-1">
                                🎉 Completo!
                            </div>
                        <?php elseif ($is_current && !$monitoring_mode): ?>
                            <a href="<?php echo $status_key; ?>.php" class="bg-secondary text-white hover:opacity-90 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-300 inline-block">
                                Continuar Agora
                            </a>
                        <?php elseif ($is_current && $monitoring_mode): ?>
                            <span class="text-sm text-secondary font-semibold">Em Progresso</span>
                        <?php else: ?>
                            <span class="text-sm text-gray-500 font-semibold">Bloqueado</span>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>

            </div>

        </div>
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="container mx-auto px-4 py-6 text-center max-w-6xl">
            <p class="text-sm text-gray-600">© 2025 BeanCode. Programar é Mágico! ✨</p>
        </div>
    </footer>

</body>
</html>