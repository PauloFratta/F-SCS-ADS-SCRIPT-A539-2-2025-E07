<?php
session_start();
include 'db.php'; 

// Redireciona se o usuário não for um responsável logado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'responsible') {
    header("Location: login.php");
    exit();
}

$responsible_id = $_SESSION['user_id'];
$responsible_name = $_SESSION['nome_responsavel'] ?? "Responsável"; 

// 1. Lógica para buscar os ALUNOS (Dependentes) cadastrados
$alunos = [];
// Usando a tabela 'alunos' e as colunas 'id', 'nome_user' e 'trilha_ativa'
$sql_alunos = "SELECT id, nome_user, trilha_ativa FROM alunos WHERE responsavel_id = ?";
$stmt_alu = $conn->prepare($sql_alunos);
$stmt_alu->bind_param("i", $responsible_id);
$stmt_alu->execute();
$result_alu = $stmt_alu->get_result();

while ($row = $result_alu->fetch_assoc()) {
    $alunos[] = $row;
}
$stmt_alu->close();

// 2. Lógica para buscar as últimas notificações/conquistas (SIMULAÇÃO)
// Em um sistema real, você faria um JOIN complexo com a tabela 'aluno_conquistas'
$notifications = [
    ['child_name' => 'Joãozinho', 'message' => 'Desbloqueou o emblema "Mago do Loop" na trilha Iniciante!', 'time' => '10 minutos atrás'],
    ['child_name' => 'Mariazinha', 'message' => 'Concluiu o Módulo 1 da trilha Criador de Jogos.', 'time' => 'Ontem'],
];

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BeanCode - Painel do Responsável</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root {
      --background: oklch(0.98 0.02 280); --foreground: oklch(0.15 0.05 260);
      --card: oklch(1 0 0); --primary: oklch(0.55 0.15 280);
      --secondary: oklch(0.75 0.12 45); --border: oklch(0.9 0.02 280);
    }
    body { background-color: var(--background); color: var(--foreground); }
    .bg-primary { background-color: var(--primary); }
    .bg-secondary { background-color: var(--secondary); }
    .bg-card { background-color: var(--card); }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <header class="sticky top-0 z-50 w-full border-b backdrop-blur bg-white/95 border-border">
    <div class="container mx-auto flex h-16 items-center justify-between px-4 max-w-6xl">
      <div class="flex items-center space-x-2">
        <span class="text-xl font-bold text-foreground">BeanCode | Responsável</span>
      </div>
      <div class="flex items-center space-x-4">
        <span class="text-sm font-medium text-gray-600">Bem-vindo(a), <?php echo htmlspecialchars($responsible_name); ?>!</span>
        <a href="logout.php" class="px-4 py-2 text-sm font-medium rounded-lg bg-secondary text-white hover:opacity-90 transition-opacity">Sair</a>
      </div>
    </div>
  </header>

  <main class="flex-grow py-12 lg:py-20">
    <div class="container mx-auto px-4 max-w-6xl">
      <h1 class="text-4xl font-bold mb-10 text-center">Painel de Controle Familiar 👨‍👩‍👧‍👦</h1>
      
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 bg-card p-6 rounded-xl shadow-lg border border-border h-fit">
          <h2 class="text-2xl font-bold text-primary mb-4 flex items-center gap-2">
            <span class="text-3xl">🔔</span> Últimas Conquistas
          </h2>
          <div class="space-y-4">
            <?php if (!empty($notifications)): ?>
                <?php foreach ($notifications as $n): ?>
                    <div class="bg-purple-50 p-3 rounded-lg border-l-4 border-primary">
                        <p class="text-sm font-semibold text-foreground"><?php echo htmlspecialchars($n['child_name']); ?>:</p>
                        <p class="text-sm text-gray-700"><?php echo htmlspecialchars($n['message']); ?></p>
                        <p class="text-xs text-muted-foreground mt-1"><?php echo htmlspecialchars($n['time']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-gray-500">Nenhuma conquista nova ainda. ✨</p>
            <?php endif; ?>
          </div>
        </div>
        
        <div class="lg:col-span-2 space-y-8">
            
            <div class="bg-card p-8 rounded-xl shadow-lg border border-border">
                <h2 class="text-2xl font-bold text-foreground mb-4 flex items-center justify-between">
                    Programadores Cadastrados (<?php echo count($alunos); ?>)
                    <a href="registro_dependentes.php" class="bg-primary text-white px-4 py-2 text-sm rounded-lg hover:opacity-90 transition-opacity">
                        + Novo Programador
                    </a>
                </h2>
                
                <div class="space-y-4">
                    <?php if (!empty($alunos)): ?>
                        <?php foreach ($alunos as $aluno): ?>
                            <div class="flex justify-between items-center p-4 bg-muted rounded-lg border">
                                <div>
                                    <p class="text-lg font-semibold text-primary"><?php echo htmlspecialchars($aluno['nome_user']); ?></p>
                                    <p class="text-sm text-gray-600">Trilha Ativa: <?php echo ucfirst($aluno['trilha_ativa'] ?? 'Não Definida'); ?></p>
                                </div>
                                <a href="course-track.php?aluno_id=<?php echo $aluno['id']; ?>" class="text-sm font-medium text-secondary hover:underline">
                                    Monitorar Progresso
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center p-6 bg-yellow-50 rounded-lg border border-yellow-200">
                            <p class="text-lg font-medium text-yellow-800">Nenhum aluno cadastrado ainda.</p>
                            <a href="registro_dependentes.php" class="text-primary hover:underline mt-2 inline-block">Clique aqui para começar o cadastro!</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            </div>
      </div>

    </div>
  </main>

  <footer class="border-t border-gray-200 bg-white">
    <div class="container mx-auto px-4 py-6 text-center max-w-6xl">
      <p class="text-sm text-gray-600">© 2025 BeanCode. Painel de Responsável.</p>
    </div>
  </footer>

</body>
</html>