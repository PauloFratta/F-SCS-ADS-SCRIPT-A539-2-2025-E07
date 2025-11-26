<?php
// course-track.php

session_start();

// 1. Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Conecta ao banco de dados e busca o progresso
include 'db.php';

$user_id = $_SESSION['user_id'];
$course_slug = 'criador-de-jogos'; // Exemplo: Trilha de jogos

$progresso = [];
$sql = "SELECT modulo_id, status FROM progresso WHERE usuario_id = ? AND curso_slug = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $user_id, $course_slug);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $progresso[$row['modulo_id']] = $row['status'];
}

$stmt->close();
$conn->close();

// Definindo os módulos e o status default
$modulos = [
    1 => ['titulo' => 'Primeiros Passos', 'descricao' => 'Blocos, Sequências e Loops.', 'emoji' => '🌟'],
    2 => ['titulo' => 'O Criador de Jogos', 'descricao' => 'Variáveis, Condicionais (If/Else) e Colisões.', 'emoji' => '🎮'],
    3 => ['titulo' => 'Mago da Web', 'descricao' => 'Introdução ao HTML e CSS.', 'emoji' => '🧙‍♂️'],
    4 => ['titulo' => 'Super App Creator', 'descricao' => 'Javascript e Interatividade.', 'emoji' => '📱']
];

function get_module_status($module_id, $progresso, $modulos) {
    if (isset($progresso[$module_id])) {
        return $progresso[$module_id];
    }
    // Se não há progresso, verifica se o módulo anterior está completo para desbloquear
    if ($module_id > 1) {
        if (isset($progresso[$module_id - 1]) && $progresso[$module_id - 1] === 'completo') {
            return 'bloqueado'; // Começa como bloqueado até iniciar
        }
        return 'bloqueado';
    }
    return 'em_progresso'; // Começa o primeiro módulo como 'em_progresso' se não houver registro.
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <title>BrinCode - Trilha de Cursos</title>
</head>
<body>
  
  <main class="py-12 lg:py-20">
    <div class="container mx-auto px-4">
      <div class="mx-auto max-w-4xl text-center mb-12">
        <h1 class="text-4xl font-bold tracking-tight sm:text-5xl mb-4">
          Olá, <span class="text-secondary"><?php echo $_SESSION['nome_crianca'] ?? 'Aventureiro'; ?></span>! Sua Jornada de Programação!
        </h1>
        <p class="text-lg text-muted-foreground">
          Continue sua aventura para conquistar o próximo emblema. 🚀
        </p>
      </div>

      <div class="relative max-w-xl mx-auto p-4 md:p-8 bg-card rounded-xl shadow-lg border border-border">
        
        <div class="track-line"></div>

        <?php foreach ($modulos as $id => $modulo): 
            $status = get_module_status($id, $progresso, $modulos);
            $is_completed = ($status === 'completo');
            $is_current = ($status === 'em_progresso');
            $is_locked = ($status === 'bloqueado');
            $status_class = $is_completed ? 'completed' : ($is_current ? 'current' : 'locked');
            $ring_color = $is_completed ? 'primary' : ($is_current ? 'secondary' : 'border');
            $fill_color = $is_completed ? 'primary' : ($is_current ? 'secondary' : 'border');
            $text_color = $is_completed ? 'primary' : ($is_current ? 'secondary' : 'muted-foreground');
        ?>
        
        <div class="mb-12 track-node <?php echo $status_class; ?> flex items-start gap-4 <?php echo $is_locked ? 'opacity-50' : 'cursor-pointer'; ?>">
          <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center node-ring rounded-full bg-white border-4 border-<?php echo $ring_color; ?>">
            <div class="node-fill bg-<?php echo $fill_color; ?> flex items-center justify-center <?php echo $is_current ? 'animate-bounce-gentle' : ''; ?>" style="<?php echo $is_current ? 'animation-delay: 0.5s;' : ''; ?>">
                <?php if ($is_completed): ?>
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                <?php elseif ($is_current): ?>
                    <span class="text-white font-bold text-sm"><?php echo $id; ?></span>
                <?php else: ?>
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <?php endif; ?>
            </div>
          </div>
          <div class="flex-grow pt-1">
            <h3 class="text-xl font-bold text-<?php echo $text_color; ?>">Módulo <?php echo $id; ?>: <?php echo $modulo['titulo']; ?></h3>
            <p class="text-gray-600 mb-2"><?php echo $modulo['descricao']; ?></p>

            <?php if ($is_completed): ?>
                <div class="text-sm text-primary font-semibold flex items-center gap-1">
                    🎉 Completo!
                </div>
            <?php elseif ($is_current): ?>
                <button class="bg-secondary text-white hover:opacity-90 px-4 py-2 text-sm font-semibold rounded-lg transition-all duration-300">
                    Continuar Aventura!
                </button>
            <?php else: // Bloqueado ?>
                <div class="text-sm text-muted-foreground font-medium mt-2">
                    Requer a conclusão do "Módulo <?php echo $id - 1; ?>".
                </div>
            <?php endif; ?>

          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </main>

  </body>
</html>