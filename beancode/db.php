<?php
// db.php
$servername = "localhost"; // Geralmente 'localhost' para desenvolvimento local
$username = "seu_usuario"; // Mude para o seu usuário do banco de dados
$password = "sua_senha_secreta"; // Mude para sua senha
$dbname = "beancode_db"; // Mude para o nome do seu banco de dados

// Cria a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Checa a conexão
if ($conn->connect_error) {
    // Para um ambiente de produção, registre o erro e mostre uma mensagem amigável
    die("Falha na conexão mágica: " . $conn->connect_error);
}

// Opcional: Define o charset para evitar problemas de acentuação
$conn->set_charset("utf8mb4");
?>