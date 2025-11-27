<?php
// process_register.php (CÓDIGO CORRIGIDO E ROBUSTO)
session_start();
include 'db.php'; 

$error_message = '';
$success_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Uso de filter_input e Null Coalescing para evitar o Erro 500 se o campo estiver faltando
    $full_name = filter_input(INPUT_POST, 'full_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS) ?? '';
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
    $password = $_POST['password'] ?? '';
    $repeat_password = $_POST['repeat_password'] ?? '';

    if (empty($password) || $password !== $repeat_password) {
        $error_message = "As senhas não coincidem ou estão vazias. 🧙‍♀️";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // SQL: Inserção na tabela 'responsaveis' (de acordo com a esquemática SQL)
        $sql = "INSERT INTO responsaveis (nome_completo, email, senha) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("sss", $full_name, $email, $password_hash);
            
            if ($stmt->execute()) {
                // Cadastro bem-sucedido.
                header("Location: login.php?status=registered");
                exit();
            } else {
                // Erro do BD (ex: e-mail duplicado)
                $error_message = "Erro ao cadastrar: E-mail Mágico pode já estar em uso.";
            }
            $stmt->close();
        } else {
            $error_message = "Erro de preparação de consulta: " . $conn->error;
        }
    }
}

$conn->close();

if ($error_message) {
    // Redireciona para o index com a mensagem de erro
    header("Location: index.php?error=" . urlencode($error_message));
    exit();
}
header("Location: index.php"); 
exit();
?>