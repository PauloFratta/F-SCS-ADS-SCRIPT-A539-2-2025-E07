<?php
// process_register.php (REVISADO)

session_start();
// Tenta incluir db.php (Se falhar aqui, o db.php está com problema de sintaxe ou credencial)
include 'db.php'; 

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Usar filter_input para garantir que os dados do POST existam e sejam sanitizados
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
    $password = $_POST['password'] ?? '';
    $repeat_password = $_POST['repeat_password'] ?? '';

    if (empty($password) || $password !== $repeat_password) {
        $error_message = "As senhas não coincidem ou estão vazias.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // SQL: Inserção na tabela 'responsaveis'.
        // **ATENÇÃO:** Confirme que as colunas 'nome_completo', 'email' e 'senha_hash' existem
        // e estão escritas EXATAMENTE assim na sua tabela 'responsaveis'.
        $sql = "INSERT INTO responsaveis (nome_completo, email, senha) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sss", $full_name, $email, $password_hash);
            
            if ($stmt->execute()) {
                // Cadastro bem-sucedido.
                // Redireciona para o login.
                header("Location: login.php?status=registered");
                exit();
            } else {
                // Falha na execução (provavelmente email duplicado ou problema de coluna)
                $error_message = "Erro ao cadastrar. Verifique se o e-mail já está em uso. Erro do BD: " . $stmt->error;
            }
            $stmt->close();
        } else {
            // Falha na preparação da consulta (coluna errada ou sintaxe SQL)
            $error_message = "Erro de preparação de consulta: " . $conn->error;
        }
    }
}

$conn->close();

// Se houver erro, redireciona para o login (para que a mensagem de erro possa ser exibida lá)
if ($error_message) {
    header("Location: login.php?error=" . urlencode($error_message));
    exit();
}
header("Location: index.php"); 
exit();
?>