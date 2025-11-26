<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BeanCode - Cadastrar Dependentes</title>
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
    .text-foreground { color: var(--foreground); }
    .border-border { border-color: var(--border); }
  </style>
</head>
<body class="min-h-screen flex flex-col">
<?php
// PHP de simulação:
// Em um ambiente real, você faria um session_start();
// e recuperaria o ID do responsável logado aqui.
$responsible_name = "Nome do Responsável"; // Simulação
$user_id = 123; // Simulação do ID do responsável
?>

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
        <span class="text-sm font-medium text-gray-600">Bem-vindo(a), <?php echo htmlspecialchars($responsible_name); ?>!</span>
        <a href="logout.php" class="px-4 py-2 text-sm font-medium rounded-lg bg-secondary text-white hover:opacity-90 transition-opacity">Sair</a>
      </div>
    </div>
  </header>

  <main class="flex-grow py-12 lg:py-20 bg-purple-50">
    <div class="container mx-auto px-4 max-w-2xl">
      <div class="bg-card p-8 rounded-xl shadow-lg border border-gray-200">
        <h1 class="text-3xl font-bold text-center mb-2">Quem Vai Programar? 🎮</h1>
        <p class="text-center text-gray-600 mb-8">
          Agora, adicione o cadastro de seu(s) filho(s)/dependente(s).
        </p>

        <form action="process_dependents.php" method="POST" class="space-y-6">
          
          <input type="hidden" name="responsible_id" value="<?php echo $user_id; ?>">

          <div id="dependents-container" class="space-y-6">
            </div>

          <button type="button" onclick="addDependentField()" class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold rounded-lg border-2 border-primary text-primary hover:bg-primary hover:text-white transition-colors">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Adicionar Outro Programador
          </button>
          
          <hr class="border-gray-200">

          <button type="submit" class="w-full bg-primary text-white py-3 rounded-lg font-semibold hover:opacity-90 transition-opacity shadow-md">
            Continuar para o Painel!
          </button>
          
          <div class="text-center">
            <a href="dashboard.php" class="text-sm text-gray-500 hover:text-primary transition-colors hover:underline" onclick="return confirm('Deseja pular esta etapa? Você poderá adicionar dependentes mais tarde.')">Pular esta etapa por enquanto</a>
          </div>
        </form>

      </div>
    </div>
  </main>

  <footer class="border-t border-gray-200 bg-white">
    <div class="container mx-auto px-4 py-6 text-center">
      <p class="text-sm text-gray-600">© 2025 BeanCode. Feito com ❤️ para jovens programadores.</p>
    </div>
  </footer>

  <script>
    let dependentCount = 0;

    /**
     * Adiciona um novo bloco de formulário para um dependente.
     */
    function addDependentField() {
      dependentCount++;

      const container = document.getElementById('dependents-container');
      
      const newDependentDiv = document.createElement('div');
      newDependentDiv.id = `dependent-${dependentCount}`;
      newDependentDiv.className = 'border border-dashed border-purple-300 p-4 rounded-lg bg-purple-50 space-y-3 relative';
      
      newDependentDiv.innerHTML = `
        <h3 class="text-lg font-semibold text-primary mb-3">Programador #${dependentCount}</h3>
        
        <button type="button" onclick="removeDependentField(${dependentCount})" class="absolute top-4 right-4 text-gray-400 hover:text-red-500 transition-colors" title="Remover Dependente">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </button>

        <div>
            <label for="name_${dependentCount}" class="block text-sm font-medium text-foreground mb-1">Nome Completo do Programador</label>
            <input type="text" id="name_${dependentCount}" name="dependents[${dependentCount}][name]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
        </div>
        
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="nickname_${dependentCount}" class="block text-sm font-medium text-foreground mb-1">Apelido (opcional)</label>
                <input type="text" id="nickname_${dependentCount}" name="dependents[${dependentCount}][nickname]" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
            </div>
            <div>
                <label for="age_${dependentCount}" class="block text-sm font-medium text-foreground mb-1">Idade (6-14)</label>
                <input type="number" id="age_${dependentCount}" name="dependents[${dependentCount}][age]" min="6" max="14" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
            </div>
        </div>

        <div>
            <label for="course_${dependentCount}" class="block text-sm font-medium text-foreground mb-1">Nível de Programação</label>
            <select id="course_${dependentCount}" name="dependents[${dependentCount}][course]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary bg-white">
                <option value="" disabled selected>Escolha um nível</option>
                <option value="iniciante">Iniciante (Blocos)</option>
                <option value="intermediario">Intermediário (Criador de Jogos)</option>
                <option value="avancado">Avançado (Mago da Web)</option>
            </select>
        </div>

        <div>
            <label for="password_${dependentCount}" class="block text-sm font-medium text-foreground mb-1">Senha do Programador</label>
            <div class="relative">
                <input type="password" id="password_${dependentCount}" name="dependents[${dependentCount}][password]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                <button type="button" onclick="togglePasswordVisibility('password_${dependentCount}', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>

        <div>
            <label for="repeat_password_${dependentCount}" class="block text-sm font-medium text-foreground mb-1">Repetir Senha</label>
            <div class="relative">
                <input type="password" id="repeat_password_${dependentCount}" name="dependents[${dependentCount}][repeat_password]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                <button type="button" onclick="togglePasswordVisibility('repeat_password_${dependentCount}', this)" class="absolute inset-y-0 right-0 pr-3 flex items-center text-sm leading-5">
                    <svg class="h-5 w-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>
      `;
      
      container.appendChild(newDependentDiv);
      newDependentDiv.scrollIntoView({ behavior: 'smooth', block: 'center' }); // Foca no novo campo
    }

    /**
     * Remove um bloco de formulário de dependente.
     * @param {number} id - O número do dependente a ser removido.
     */
    function removeDependentField(id) {
        if (confirm(`Tem certeza que deseja remover o Programador #${id}?`)) {
            const element = document.getElementById(`dependent-${id}`);
            if (element) {
                element.remove();
            }
        }
    }
    
    /**
     * Alterna a visibilidade do campo de senha (de password para text e vice-versa).
     * Replicada do index.php para garantir a funcionalidade de visualização.
     * @param {string} inputId - O ID do campo de input de senha.
     * @param {HTMLElement} buttonElement - O botão de alternância que foi clicado.
     */
    function togglePasswordVisibility(inputId, buttonElement) {
        const passwordInput = document.getElementById(inputId);
        const isPassword = passwordInput.type === 'password';

        // 1. Altera o tipo do input
        passwordInput.type = isPassword ? 'text' : 'password';

        // 2. Altera o ícone do botão
        const svg = buttonElement.querySelector('svg');
        if (isPassword) {
            // Mostra Senha: Ícone do Olho Aberto
            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.97 9.97 0 011.666-3.235m1.52-1.52c.24-.24.512-.45.81-.628M15 12a3 3 0 11-6 0 3 3 0 016 0zm-3 3a3 3 0 100-6 3 3 0 000 6z"/>';
        } else {
            // Esconde Senha: Ícone do Olho Cortado
            svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    }


    // Adiciona o primeiro dependente ao carregar a página
    window.onload = function() {
      addDependentField();
    };
  </script>
</body>
</html>