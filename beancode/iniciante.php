<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_name = $_SESSION['nome_responsavel'] ?? $_SESSION['nome_aluno'] ?? "Aluno";
$track_title = "Primeiros Passos";
$track_emoji = "🌟";
$track_color = "text-primary";
$track_bg = "bg-purple-100";

// Pega o ID da lição da URL (padrão: lição 1)
$licao_id = isset($_GET['licao_id']) ? (int)$_GET['licao_id'] : 1;

// Define o conteúdo de cada lição - Lógica de Programação para Crianças
$licoes = [
    1 => [
        'titulo' => 'Lição 1.1: Sequência - Ordem Importa!',
        'subtitulo' => 'Aprenda que em programação, a ordem dos comandos é muito importante!',
        'blocos_necessarios' => 5,
        'tipo_bloco' => 'Mover (10) passos',
        'desafio' => 'Coloque 5 blocos "Mover (10) passos" NA ORDEM CERTA para o BeanCode chegar em casa!',
        'visual' => '🧙‍♂️ → → → → → 🏠',
        'conceito' => 'SEQUÊNCIA'
    ],
    2 => [
        'titulo' => 'Lição 1.2: Pensamento Lógico - Planejar Antes de Fazer',
        'subtitulo' => 'Como pensar passo a passo para resolver problemas!',
        'blocos_necessarios' => 4,
        'tipo_bloco' => 'misto',
        'desafio' => 'Use EXATAMENTE 2 blocos "Mover" e 2 blocos "Girar" para chegar ao destino!',
        'visual' => '🧙‍♂️ → ↻ → 🏠',
        'conceito' => 'PLANEJAMENTO'
    ],
    3 => [
        'titulo' => 'Lição 1.3: Decomposição - Dividir para Conquistar',
        'subtitulo' => 'Grandes problemas ficam fáceis quando dividimos em partes pequenas!',
        'blocos_necessarios' => 6,
        'tipo_bloco' => 'qualquer',
        'desafio' => 'Use 6 blocos para completar o caminho. Pense: quantos passos para frente? Precisa virar?',
        'visual' => '🧙‍♂️ → → ↻ → → 🏠',
        'conceito' => 'DECOMPOSIÇÃO'
    ]
];

$licao_atual = $licoes[$licao_id] ?? $licoes[1];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BeanCode - <?php echo htmlspecialchars($licao_atual['titulo']); ?></title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
    /* Estilos base mantidos */
    :root {
      --background: oklch(0.98 0.02 280);
      --foreground: oklch(0.15 0.05 260);
      --card: oklch(1 0 0);
      --primary: oklch(0.55 0.15 280);
      --secondary: oklch(0.75 0.12 45);
    }
    body { background-color: var(--background); color: var(--foreground); font-family: system-ui, -apple-system, sans-serif; }
    .bg-primary { background-color: var(--primary); }
    .bg-secondary { background-color: var(--secondary); }
    .text-primary { color: var(--primary); }
    .border-border { border-color: var(--border); }
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

      <nav class="flex items-center space-x-4">
        <a href="trilhas.php" class="px-4 py-2 text-sm font-medium rounded-lg hover:bg-gray-100 transition-colors">Voltar para Trilhas</a>
        <a href="logout.php" class="px-4 py-2 text-sm font-medium rounded-lg bg-secondary text-white hover:opacity-90 transition-opacity">Sair</a>
      </nav>
    </div>
  </header>

  <main class="flex-grow py-12 lg:py-20">
    <div class="container mx-auto px-4 max-w-4xl">
      <div class="bg-card p-8 rounded-xl shadow-2xl border-t-8 border-primary">
        
        <div class="text-center mb-8">
            <span class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-full <?php echo $track_bg . ' ' . $track_color; ?>">
                <?php echo $track_emoji; ?> Trilha: <?php echo $track_title; ?>
            </span>
            <h1 class="text-4xl font-bold mt-4"><?php echo htmlspecialchars($licao_atual['titulo']); ?></h1>
            <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($licao_atual['subtitulo']); ?></p>
        </div>

        <hr class="mb-8 border-gray-200">

        <section class="space-y-8">
            <!-- Badge do Conceito -->
            <div class="flex justify-center">
                <span class="inline-flex items-center gap-2 px-6 py-3 text-lg font-bold rounded-full bg-gradient-to-r from-purple-500 to-pink-500 text-white shadow-lg">
                    🎯 Conceito: <?php echo $licao_atual['conceito']; ?>
                </span>
            </div>

            <?php if ($licao_id == 1): ?>
                <h2 class="text-2xl font-semibold text-primary">📝 O que é SEQUÊNCIA?</h2>
                <div class="bg-blue-50 p-6 rounded-lg border-2 border-blue-200">
                    <p class="text-lg mb-4">Imagine que você está ensinando um robô a fazer um sanduíche. A ordem importa muito!</p>
                    <div class="bg-white p-4 rounded-lg mb-4">
                        <p class="font-bold text-red-600 mb-2">❌ ERRADO (ordem errada):</p>
                        <ol class="list-decimal list-inside text-gray-700 space-y-1 ml-4">
                            <li>Colocar o queijo</li>
                            <li>Pegar o pão</li>
                            <li>Cortar o pão</li>
                        </ol>
                    </div>
                    <div class="bg-white p-4 rounded-lg">
                        <p class="font-bold text-green-600 mb-2">✅ CERTO (ordem correta):</p>
                        <ol class="list-decimal list-inside text-gray-700 space-y-1 ml-4">
                            <li>Pegar o pão</li>
                            <li>Cortar o pão</li>
                            <li>Colocar o queijo</li>
                        </ol>
                    </div>
                </div>

                <h2 class="text-2xl font-semibold text-primary">🎮 Como isso funciona aqui?</h2>
                <div class="bg-purple-100 p-6 rounded-lg border border-purple-300">
                    <p class="text-lg mb-4">Você vai arrastar blocos "Mover" para criar uma SEQUÊNCIA de comandos. O BeanCode vai seguir cada bloco NA ORDEM que você colocar!</p>
                    <div class="flex items-center justify-center gap-3 text-3xl my-4">
                        <span>1️⃣</span> <span>➡️</span>
                        <span>2️⃣</span> <span>➡️</span>
                        <span>3️⃣</span> <span>➡️</span>
                        <span>4️⃣</span> <span>➡️</span>
                        <span>5️⃣</span>
                    </div>
                    <p class="text-gray-700 font-medium text-center">Cada comando acontece depois do anterior!</p>
                </div>
            <?php elseif ($licao_id == 2): ?>
                <h2 class="text-2xl font-semibold text-primary">🧠 O que é PENSAMENTO LÓGICO?</h2>
                <div class="bg-green-50 p-6 rounded-lg border-2 border-green-200">
                    <p class="text-lg mb-4">É como pensar igual um detetive! Antes de fazer algo, você precisa PLANEJAR:</p>
                    <div class="bg-white p-4 rounded-lg space-y-3">
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">1️⃣</span>
                            <div>
                                <p class="font-bold text-blue-600">ENTENDER o problema</p>
                                <p class="text-gray-600 text-sm">Para onde o BeanCode precisa ir?</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">2️⃣</span>
                            <div>
                                <p class="font-bold text-blue-600">PENSAR nas possibilidades</p>
                                <p class="text-gray-600 text-sm">Quais blocos eu posso usar?</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-2xl">3️⃣</span>
                            <div>
                                <p class="font-bold text-blue-600">ESCOLHER a melhor solução</p>
                                <p class="text-gray-600 text-sm">Qual ordem de blocos vai funcionar?</p>
                            </div>
                        </div>
                    </div>
                </div>

                <h2 class="text-2xl font-semibold text-primary">🎯 Seu Desafio de Lógica</h2>
                <div class="bg-yellow-100 p-6 rounded-lg border border-yellow-300">
                    <p class="text-lg mb-3">Desta vez você TEM que usar tipos diferentes de blocos!</p>
                    <ul class="list-disc list-inside text-gray-700 space-y-2 ml-4">
                        <li>➡️ Use <strong>2 blocos "Mover"</strong> para ir para frente</li>
                        <li>🔄 Use <strong>2 blocos "Girar"</strong> para mudar de direção</li>
                        <li>🤔 Pense: em qual ordem colocar eles?</li>
                    </ul>
                </div>
            <?php elseif ($licao_id == 3): ?>
                <h2 class="text-2xl font-semibold text-primary">🔍 O que é DECOMPOSIÇÃO?</h2>
                <div class="bg-orange-50 p-6 rounded-lg border-2 border-orange-200">
                    <p class="text-lg mb-4">É dividir um problema GRANDE em pedaços PEQUENOS! Como comer uma pizza:</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div class="bg-white p-4 rounded-lg border-2 border-red-300">
                            <p class="font-bold text-red-600 mb-2">❌ Difícil:</p>
                            <p class="text-gray-700">"Coma a pizza inteira de uma vez!"</p>
                            <div class="text-center text-4xl mt-2">🍕</div>
                        </div>
                        <div class="bg-white p-4 rounded-lg border-2 border-green-300">
                            <p class="font-bold text-green-600 mb-2">✅ Fácil:</p>
                            <p class="text-gray-700">"Coma um pedaço de cada vez!"</p>
                            <div class="text-center text-2xl mt-2">🍕 → 🍕 → 🍕 → 🍕</div>
                        </div>
                    </div>
                </div>

                <h2 class="text-2xl font-semibold text-primary">🎯 Como decompor este desafio?</h2>
                <div class="bg-purple-100 p-6 rounded-lg border border-purple-300">
                    <p class="text-lg mb-4 font-bold">Divida o problema em perguntas menores:</p>
                    <ol class="list-decimal list-inside text-gray-700 space-y-3 ml-4">
                        <li><strong>Quantos passos para frente?</strong> (Use blocos Mover)</li>
                        <li><strong>Precisa virar em algum momento?</strong> (Use blocos Girar)</li>
                        <li><strong>Depois de virar, quantos passos mais?</strong> (Mais blocos Mover)</li>
                    </ol>
                    <div class="mt-4 p-3 bg-yellow-100 rounded-lg">
                        <p class="text-sm text-gray-700">💡 <strong>Dica:</strong> Olhe o caminho e conte: você precisa ir reto duas vezes, e virar uma vez no meio!</p>
                    </div>
                </div>
            <?php endif; ?>

            <h2 class="text-2xl font-semibold text-primary">Seu Desafio 🚀</h2>
            <div class="bg-yellow-100 p-6 rounded-lg border border-yellow-300 space-y-3">
                <p class="text-lg font-medium"><?php echo $licao_atual['desafio']; ?></p>
                <div class="text-center text-4xl">
                    <?php echo $licao_atual['visual']; ?>
                </div>
                <button onclick="openEditor()" class="w-full bg-secondary text-white py-3 rounded-lg font-semibold hover:opacity-90 transition-opacity mt-4">
                    Abrir Editor de Blocos
                </button>
            </div>
        </section>

      </div>
    </div>
  </main>

  <!-- Modal Editor de Blocos -->
  <div id="editorModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-4 p-6">
      <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-primary">🧩 Editor de Blocos</h2>
        <button onclick="closeEditor()" class="text-gray-500 hover:text-gray-700 text-3xl leading-none">&times;</button>
      </div>

      <div class="mb-6">
        <?php if ($licao_id == 1): ?>
          <p class="text-gray-700 mb-4">🎯 <strong>Lição 1.1 - SEQUÊNCIA:</strong> Arraste 5 blocos "Mover" na ordem certa para criar uma sequência!</p>
          <div class="text-center text-4xl mb-6 bg-gray-100 p-4 rounded">
            <span>🌱</span> <span>→</span> <span>→</span> <span>→</span> <span>→</span> <span>→</span> <span>🏠</span>
          </div>
          <p class="text-sm text-gray-600 text-center">Lembre: cada bloco precisa vir um depois do outro!</p>
        <?php elseif ($licao_id == 2): ?>
          <p class="text-gray-700 mb-4">🧠 <strong>Lição 1.2 - PLANEJAMENTO:</strong> Pense antes! Use 2 blocos Mover E 2 blocos Girar.</p>
          <div class="text-center text-4xl mb-6 bg-gray-100 p-4 rounded">
            <span>🌱</span> <span>→</span> <span>🔄</span> <span>→</span> <span>🏠</span>
          </div>
          <p class="text-sm text-gray-600 text-center">Qual é a melhor ordem para combinar movimento e rotação?</p>
        <?php elseif ($licao_id == 3): ?>
          <p class="text-gray-700 mb-4">🔍 <strong>Lição 1.3 - DECOMPOSIÇÃO:</strong> Divida o problema! Use 6 blocos de qualquer tipo.</p>
          <div class="text-center text-4xl mb-6 bg-gray-100 p-4 rounded">
            <span>🌱</span> <span>→</span> <span>→</span> <span>🔄</span> <span>→</span> <span>→</span> <span>🏠</span>
          </div>
          <p class="text-sm text-gray-600 text-center">Quebre em etapas: ir reto → virar → ir reto de novo!</p>
        <?php endif; ?>
      </div>

      <!-- Blocos Disponíveis -->
      <div class="mb-6">
        <h3 class="font-semibold mb-3">📦 Blocos Disponíveis:</h3>
        <div class="bg-blue-50 p-4 rounded-lg border-2 border-blue-300" id="availableBlocks">
          <!-- Blocos serão adicionados via JS -->
        </div>
      </div>

      <!-- Área de Código -->
      <div class="mb-6">
        <h3 class="font-semibold mb-3">📝 Área de Código (arraste os blocos aqui):</h3>
        <div id="codeArea" class="bg-gray-100 p-4 rounded-lg border-2 border-dashed border-gray-400 min-h-[200px]" ondrop="drop(event)" ondragover="allowDrop(event)">
          <p class="text-gray-400 text-center" id="emptyText">Arraste os blocos para cá...</p>
        </div>
      </div>

      <!-- Resultado -->
      <div id="result" class="mb-4 p-4 rounded-lg hidden"></div>

      <!-- Botões -->
      <div class="flex gap-3">
        <button onclick="submitCode()" class="flex-1 bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700">
          ✅ Enviar Solução
        </button>
        <button onclick="clearCode()" class="px-6 bg-red-500 text-white py-3 rounded-lg font-semibold hover:bg-red-600">
          🗑️ Limpar
        </button>
      </div>
    </div>
  </div>

  <footer class="border-t border-gray-200 bg-white">
    <div class="container mx-auto px-4 py-6 text-center">
      <p class="text-sm text-gray-600">© 2025 BeanCode. Programar é incrível!</p>
    </div>
  </footer>

  <script>
    let blockCounter = 0;

    function openEditor() {
      document.getElementById('editorModal').classList.remove('hidden');
      document.getElementById('editorModal').classList.add('flex');
      initializeBlocks();
    }

    function closeEditor() {
      document.getElementById('editorModal').classList.add('hidden');
      document.getElementById('editorModal').classList.remove('flex');
    }

    function initializeBlocks() {
      const container = document.getElementById('availableBlocks');
      container.innerHTML = '';
      
      const blockTypes = [
        { text: '➡️ Mover (10) passos', color: 'bg-blue-500', count: 5 },
        { text: '🔄 Girar 90°', color: 'bg-green-500', count: 3 },
        { text: '⬆️ Pular', color: 'bg-yellow-500', count: 2 },
        { text: '🔊 Emitir Som', color: 'bg-purple-500', count: 2 }
      ];
      
      let blockId = 0;
      blockTypes.forEach(type => {
        for (let i = 0; i < type.count; i++) {
          const block = createBlock(blockId, type.text, type.color);
          container.appendChild(block);
          blockId++;
        }
      });
    }

    function createBlock(id, text, color) {
      const block = document.createElement('div');
      block.className = `inline-block ${color} text-white px-4 py-3 rounded-lg font-semibold m-1 cursor-move shadow`;
      block.draggable = true;
      block.id = 'block-' + id;
      block.textContent = text;
      block.setAttribute('data-text', text);
      block.setAttribute('data-color', color);
      block.ondragstart = drag;
      return block;
    }

    function allowDrop(ev) {
      ev.preventDefault();
    }

    function drag(ev) {
      ev.dataTransfer.setData('text', ev.target.id);
    }

    function drop(ev) {
      ev.preventDefault();
      const data = ev.dataTransfer.getData('text');
      const draggedElement = document.getElementById(data);
      
      if (draggedElement) {
        const codeArea = document.getElementById('codeArea');
        const emptyText = document.getElementById('emptyText');
        
        if (emptyText) {
          emptyText.remove();
        }

        // Salva os dados do bloco antes de mover
        const blockText = draggedElement.getAttribute('data-text');
        const blockColor = draggedElement.getAttribute('data-color');
        const originalId = draggedElement.id;
        
        // Move o bloco original para a área de código
        draggedElement.classList.remove('m-1', 'cursor-move');
        draggedElement.classList.add('block', 'mb-2', 'relative');
        draggedElement.draggable = false;
        draggedElement.ondragstart = null;
        draggedElement.setAttribute('data-original-id', originalId);
        
        const removeBtn = document.createElement('button');
        removeBtn.textContent = '×';
        removeBtn.className = 'absolute right-2 top-2 text-white hover:text-red-200 font-bold text-xl';
        removeBtn.onclick = function() {
          // Retorna o bloco para a área de blocos disponíveis
          const block = createBlock(originalId.replace('block-', ''), blockText, blockColor);
          block.id = originalId;
          document.getElementById('availableBlocks').appendChild(block);
          // Remove o bloco da área de código
          draggedElement.remove();
          checkEmpty();
        };
        
        draggedElement.appendChild(removeBtn);
        codeArea.appendChild(draggedElement);
      }
    }

    function returnBlockToAvailable(blockId) {
      const availableBlocks = document.getElementById('availableBlocks');
      const removedBlock = document.querySelector(`#codeArea [data-original-id="${blockId}"]`);
      
      if (removedBlock) {
        const text = removedBlock.getAttribute('data-text');
        const color = removedBlock.getAttribute('data-color');
        const block = createBlock(blockId.replace('block-', ''), text, color);
        block.id = blockId;
        availableBlocks.appendChild(block);
      }
    }

    function checkEmpty() {
      const codeArea = document.getElementById('codeArea');
      const blocks = codeArea.getElementsByClassName('block');
      if (blocks.length === 0) {
        codeArea.innerHTML = '<p class="text-gray-400 text-center" id="emptyText">Arraste os blocos para cá...</p>';
      }
    }

    function clearCode() {
      const codeArea = document.getElementById('codeArea');
      codeArea.innerHTML = '<p class="text-gray-400 text-center" id="emptyText">Arraste os blocos para cá...</p>';
      document.getElementById('result').classList.add('hidden');
    }

    function submitCode() {
      const codeArea = document.getElementById('codeArea');
      const blocks = codeArea.getElementsByClassName('block');
      const result = document.getElementById('result');
      const requiredBlocks = <?php echo $licao_atual['blocos_necessarios']; ?>;
      const licaoId = <?php echo $licao_id; ?>;
      
      result.classList.remove('hidden');
      
      let isCorrect = false;
      let feedbackMessage = '';
      
      // Validação específica por lição
      if (licaoId === 1) {
        // Lição 1.1: Precisa de 5 blocos "Mover"
        let moverCount = 0;
        for (let block of blocks) {
          if (block.textContent.includes('Mover')) {
            moverCount++;
          }
        }
        isCorrect = (moverCount === 5);
        feedbackMessage = isCorrect ? 
          'O BeanCode chegou em casa com sucesso!' : 
          `Você usou ${moverCount} bloco(s) "Mover". Precisa de exatamente 5!`;
      } else if (licaoId === 2) {
        // Lição 1.2: Precisa de 2 blocos "Mover" e 2 blocos "Girar"
        let moverCount = 0;
        let girarCount = 0;
        for (let block of blocks) {
          if (block.textContent.includes('Mover')) {
            moverCount++;
          } else if (block.textContent.includes('Girar')) {
            girarCount++;
          }
        }
        isCorrect = (moverCount === 2 && girarCount === 2);
        if (isCorrect) {
          feedbackMessage = 'Perfeito! Você planejou direitinho!';
        } else {
          feedbackMessage = `Você usou ${moverCount} bloco(s) "Mover" e ${girarCount} bloco(s) "Girar". Precisa de exatamente 2 de cada!`;
        }
      } else if (licaoId === 3) {
        // Lição 1.3: Precisa de 6 blocos de qualquer tipo
        isCorrect = (blocks.length === 6);
        feedbackMessage = isCorrect ? 
          'Incrível! Você dominou a decomposição!' : 
          `Você usou ${blocks.length} bloco(s). Precisa de exatamente 6 blocos (podem ser de qualquer tipo)!`;
      }
      
      if (isCorrect) {
        result.className = 'mb-4 p-4 rounded-lg bg-green-100 border border-green-300';
        result.innerHTML = `
          <div class="flex items-center gap-3">
            <span class="text-3xl">🎉</span>
            <div>
              <p class="font-bold text-green-800">Parabéns! Você completou o desafio!</p>
              <p class="text-sm text-green-700">${feedbackMessage}</p>
            </div>
          </div>
        `;
        
        // Marca a tarefa como concluída e mostra animação
        markTaskComplete();
      } else {
        result.className = 'mb-4 p-4 rounded-lg bg-yellow-100 border border-yellow-300';
        result.innerHTML = `
          <div class="flex items-center gap-3">
            <span class="text-3xl">🤔</span>
            <div>
              <p class="font-bold text-yellow-800">Quase lá!</p>
              <p class="text-sm text-yellow-700">${feedbackMessage}</p>
            </div>
          </div>
        `;
      }
    }

    function markTaskComplete() {
      // Salva o progresso no banco de dados
      fetch('save_progress.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'licao_id=<?php echo $licao_id; ?>'
      })
      .then(response => {
        console.log('Response status:', response.status);
        return response.text();
      })
      .then(text => {
        console.log('Response text:', text);
        try {
          const data = JSON.parse(text);
          if (data.success) {
            // Cria overlay de animação
            const overlay = document.createElement('div');
            overlay.className = 'fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[100]';
            overlay.innerHTML = `
              <div class="text-center">
                <div class="mb-6">
                  <div id="sprout" class="transition-all duration-1000 ease-out" style="font-size: 2rem;">🌱</div>
                </div>
                <h2 class="text-4xl font-bold text-white mb-2">✨ Tarefa Concluída! ✨</h2>
                <p class="text-xl text-green-300">Seu BeanCode está crescendo!</p>
              </div>
            `;
            document.body.appendChild(overlay);

            // Animação de crescimento
            setTimeout(() => {
              const sprout = document.getElementById('sprout');
              if (sprout) {
                sprout.style.fontSize = '8rem';
                sprout.style.transform = 'rotate(360deg)';
              }
            }, 100);

            // Efeitos de partículas mágicas
            for (let i = 0; i < 20; i++) {
              setTimeout(() => {
                createMagicParticle(overlay);
              }, i * 50);
            }

            // Fecha a animação e redireciona para trilhas.php após 3 segundos
            setTimeout(() => {
              overlay.style.opacity = '0';
              overlay.style.transition = 'opacity 0.5s';
              setTimeout(() => {
                window.location.href = 'trilhas.php';
              }, 500);
            }, 3000);
          } else {
            console.error('Erro ao salvar progresso:', data.message);
            alert('Erro ao salvar progresso. Tente novamente.');
          }
        } catch (e) {
          console.error('Erro ao parsear JSON:', e);
          console.error('Resposta recebida:', text);
          alert('Erro ao processar resposta do servidor.');
        }
      })
      .catch(error => {
        console.error('Erro na requisição:', error);
        alert('Erro ao conectar com o servidor.');
      });
    }

    function createMagicParticle(container) {
      const particle = document.createElement('div');
      const symbols = ['✨', '⭐', '🌟', '💫', '🪴'];
      particle.textContent = symbols[Math.floor(Math.random() * symbols.length)];
      particle.className = 'absolute text-2xl pointer-events-none';
      
      const startX = Math.random() * window.innerWidth;
      const startY = window.innerHeight / 2;
      particle.style.left = startX + 'px';
      particle.style.top = startY + 'px';
      particle.style.transition = 'all 1.5s ease-out';
      
      container.appendChild(particle);
      
      setTimeout(() => {
        particle.style.transform = `translate(${(Math.random() - 0.5) * 400}px, ${-300 - Math.random() * 200}px) rotate(${Math.random() * 720}deg)`;
        particle.style.opacity = '0';
      }, 50);
      
      setTimeout(() => particle.remove(), 1600);
    }
  </script>

</body>
</html>