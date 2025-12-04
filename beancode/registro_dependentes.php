<?php
// registro_dependentes.php
session_start();
include 'db.php'; 

// Verifica se o responsável está logado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'responsible') {
    header("Location: index.php");
    exit();
}

$responsible_name = $_SESSION['nome_responsavel'] ?? "Responsável"; 
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BeanCode - Cadastrar Alunos</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    :root {
      --background: oklch(0.98 0.02 280);
      --foreground: oklch(0.15 0.05 260);
      --card: oklch(1 0 0);
      --primary: oklch(0.55 0.15 280);
      --secondary: oklch(0.75 0.12 45);
      --border: oklch(0.9 0.02 280);
    }
    body { background-color: var(--background); color: var(--foreground); }
    .bg-primary { background-color: var(--primary); }
    .bg-secondary { background-color: var(--secondary); }
    .bg-card { background-color: var(--card); }
    .border-border { border-color: var(--border); }
  </style>
</head>
<body class="min-h-screen flex flex-col">

  <header class="sticky top-0 z-50 w-full border-b backdrop-blur bg-white/95 border-border">
    <div class="container mx-auto flex h-16 items-center justify-between px-4 max-w-6xl">
      <div class="flex items-center space-x-2">
        <span class="text-xl font-bold text-foreground">BeanCode | Cadastro de Alunos</span>
      </div>
      <div class="flex items-center space-x-4">
        <a href="dashboard_responsavel.php" class="px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-100 transition-colors">Voltar ao Dashboard</a>
        <a href="logout.php" class="px-4 py-2 text-sm font-medium rounded-lg bg-secondary text-white hover:opacity-90 transition-opacity">Sair</a>
      </div>
    </div>
  </header>

  <main class="flex-grow py-12 lg:py-20">
    <div class="container mx-auto px-4 max-w-4xl">
      <div class="bg-card p-8 rounded-xl shadow-2xl border border-border">
        <h1 class="text-4xl font-bold mb-6 text-center text-primary">Cadastrar Novos Programadores 🧙‍♂️</h1>
        <p class="text-center text-gray-600 mb-8">Adicione os pequenos programadores que vão começar a jornada mágica!</p>

        <form method="POST" action="process_dependents.php" id="dependents-form">
          <div id="dependents-container">
            <!-- Primeiro aluno -->
            <div class="dependent-item bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6">
              <h3 class="text-xl font-semibold mb-4 text-primary">Programador #1</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-sm font-medium mb-2">Nome do Aluno</label>
                  <input 
                    type="text" 
                    name="dependents[0][name]" 
                    placeholder="Ex: João" 
                    required 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium mb-2">Senha do Aluno</label>
                  <input 
                    type="password" 
                    name="dependents[0][password]" 
                    placeholder="********" 
                    required 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary"
                  >
                </div>

                <div>
                  <label class="block text-sm font-medium mb-2">Repetir Senha</label>
                  <input 
                    type="password" 
                    name="dependents[0][repeat_password]" 
                    placeholder="********" 
                    required 
                    class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary"
                  >
                </div>
              </div>
            </div>
          </div>

          <div class="flex gap-4 mt-6">
            <button 
              type="button" 
              onclick="addDependent()" 
              class="flex-1 px-6 py-3 text-sm font-medium rounded-lg border-2 border-primary text-primary hover:bg-purple-50 transition-colors"
            >
              + Adicionar Outro Aluno
            </button>
            
            <button 
              type="submit" 
              class="flex-1 px-6 py-3 text-sm font-medium rounded-lg bg-primary text-white hover:opacity-90 transition-opacity"
            >
              Cadastrar Todos
            </button>
          </div>
        </form>
      </div>
    </div>
  </main>

  <footer class="border-t border-gray-200 bg-white">
    <div class="container mx-auto px-4 py-6 text-center max-w-6xl">
      <p class="text-sm text-gray-600">© 2025 BeanCode. Cadastro de Alunos.</p>
    </div>
  </footer>

  <script>
    let dependentCount = 1;

    function addDependent() {
      const container = document.getElementById('dependents-container');
      const newDependent = document.createElement('div');
      newDependent.className = 'dependent-item bg-gray-50 p-6 rounded-lg border border-gray-200 mb-6';
      newDependent.innerHTML = `
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold text-primary">Programador #${dependentCount + 1}</h3>
          <button type="button" onclick="removeDependent(this)" class="text-red-500 hover:text-red-700 font-medium">Remover</button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm font-medium mb-2">Nome do Aluno</label>
            <input 
              type="text" 
              name="dependents[${dependentCount}][name]" 
              placeholder="Ex: Maria" 
              required 
              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary"
            >
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">Senha do Aluno</label>
            <input 
              type="password" 
              name="dependents[${dependentCount}][password]" 
              placeholder="********" 
              required 
              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary"
            >
          </div>

          <div>
            <label class="block text-sm font-medium mb-2">Repetir Senha</label>
            <input 
              type="password" 
              name="dependents[${dependentCount}][repeat_password]" 
              placeholder="********" 
              required 
              class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-primary"
            >
          </div>
        </div>
      `;
      container.appendChild(newDependent);
      dependentCount++;
    }

    function removeDependent(button) {
      button.closest('.dependent-item').remove();
    }
  </script>

</body>
</html>
