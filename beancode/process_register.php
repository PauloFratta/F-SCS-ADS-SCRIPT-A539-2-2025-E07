<?php
// process_register.php (CÓDIGO CORRIGIDO E ROBUSTO)
session_start();
include 'db.php'; 

// Verifica se a conexão foi estabelecida
if (!isset($conn) || $conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . ($conn->connect_error ?? 'Conexão não estabelecida'));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Uso de filter_input e Null Coalescing para evitar o Erro 500 se o campo estiver faltando
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $repeat_password = $_POST['repeat_password'] ?? '';

    // Debug: mostra dados recebidos
    error_log("Dados recebidos - Nome: $full_name, Email: $email");
    
    // Validação de campos vazios
    if (empty($full_name) || empty($email)) {
        error_log("Erro: Campos vazios - Nome: '$full_name', Email: '$email'");
        close_db_connection();
        header("Location: index.php?error=" . urlencode("Todos os campos são obrigatórios."));
        exit();
    }

    if (empty($password) || $password !== $repeat_password) {
        error_log("Erro: Senhas não coincidem ou vazias");
        close_db_connection();
        header("Location: index.php?error=" . urlencode("As senhas não coincidem ou estão vazias."));
        exit();
    }
    
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    error_log("Hash de senha gerado com sucesso");

    // SQL: Inserção na tabela 'responsaveis'
    $sql = "INSERT INTO responsaveis (nome_completo, email, senha) VALUES (?, ?, ?)";
    error_log("Tentando preparar SQL: $sql");
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        error_log("SQL preparado com sucesso");
        $stmt->bind_param("sss", $full_name, $email, $password_hash);
        error_log("Parâmetros vinculados, executando...");
        
        if ($stmt->execute()) {
            error_log("Cadastro realizado com sucesso para: $email");
            $stmt->close();
            close_db_connection();
            // Cadastro bem-sucedido
            header("Location: index.php?success=" . urlencode("Cadastro realizado com sucesso! Faça login abaixo."));
            exit();
        } else {
            // Erro do BD (ex: e-mail duplicado)
            $error_detail = $stmt->error;
            error_log("Erro ao executar SQL: $error_detail");
            $stmt->close();
            close_db_connection();
            
            // Verifica se é erro de duplicação
            if (strpos($error_detail, 'Duplicate') !== false || strpos($error_detail, '1062') !== false) {
                header("Location: index.php?error=" . urlencode("E-mail já está cadastrado."));
            } else {
                header("Location: index.php?error=" . urlencode("Erro ao cadastrar: " . $error_detail));
            }
            exit();
        }
    } else {
        $error_detail = $conn->error;
        error_log("Erro ao preparar SQL: $error_detail");
        close_db_connection();
        header("Location: index.php?error=" . urlencode("Erro ao preparar consulta: " . $error_detail));
        exit();
    }
} else {
    // Se não for POST, redireciona
    error_log("Requisição não é POST, redirecionando");
    header("Location: index.php");
    exit();
}
?>