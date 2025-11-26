<?php
// login.php (Versão com PHP para processar o formulário)

// Inclui o arquivo de conexão com o banco de dados
include 'db.php'; 

// Inicializa a variável de erro
$error_message = '';
$success_message = '';

// Verifica se o formulário foi submetido (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Processamento de Login
    if (isset($_POST['login_submit'])) {
        $email = $conn->real_escape_string($_POST['email']);
        $password = $_POST['password'];

        $sql = "SELECT id, senha_hash, nome_crianca FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verifica a senha
            if (password_verify($password, $user['senha_hash'])) {
                // Login bem-sucedido!
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nome_crianca'] = $user['nome_crianca'];
                
                // Redireciona para a trilha de cursos
                header("Location: course-track.php");
                exit();
            } else {
                $error_message = "Email ou senha mágica incorretos. 🧙‍♂️";
            }
        } else {
            $error_message = "Email ou senha mágica incorretos. 🧙‍♂️";
        }
        $stmt->close();
    }
    
    // Processamento de Cadastro (Exemplo simplificado)
    // Você precisaria de um formulário de cadastro separado, mas o processamento seria assim:
    /*
    if (isset($_POST['register_submit'])) {
        $email = $conn->real_escape_string($_POST['reg_email']);
        $password = password_hash($_POST['reg_password'], PASSWORD_DEFAULT);
        $nome_crianca = $conn->real_escape_string($_POST['reg_nome']);

        $sql = "INSERT INTO usuarios (email, senha_hash, nome_crianca) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $email, $password, $nome_crianca);

        if ($stmt->execute()) {
            $success_message = "Cadastro feito com sucesso! Faça login agora. 🎉";
        } else {
            $error_message = "Erro ao cadastrar. Tente outro Email Mágico.";
        }
        $stmt->close();
    }
    */
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <title>BrinCode - Login</title>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

  <div class="w-full max-w-md bg-card border border-border p-8 rounded-xl shadow-2xl relative z-10">
    <?php if ($error_message): ?>
      <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
        <span class="block sm:inline"><?php echo $error_message; ?></span>
      </div>
    <?php endif; ?>
    
    <?php if ($success_message): ?>
      <div class="bg-teal-100 border border-teal-400 text-teal-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
        <span class="block sm:inline"><?php echo $success_message; ?></span>
      </div>
    <?php endif; ?>

    <form class="space-y-6" method="POST" action="login.php">
      <div>
        <label for="email" class="block text-sm font-medium mb-2 text-foreground">
          Email Mágico
        </label>
        <input 
          type="email" 
          id="email" 
          name="email" 
          placeholder="seu@email.com" 
          required 
          class="w-full input-style"
        >
      </div>

      <div>
        <label for="password" class="block text-sm font-medium mb-2 text-foreground">
          Sua Senha Secreta
        </label>
        <input 
          type="password" 
          id="password" 
          name="password" 
          placeholder="********" 
          required 
          class="w-full input-style"
        >
      </div>

      <button 
        type="submit" 
        name="login_submit"
        class="w-full bg-primary text-primary-foreground hover:opacity-90 px-4 py-3 text-lg font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center gap-2">
        Entrar na Aventura!
        </button>

      </form>
  </div>
</body>
</html>