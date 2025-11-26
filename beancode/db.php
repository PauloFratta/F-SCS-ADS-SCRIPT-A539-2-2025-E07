<?php
// db.php
// ATENÇÃO: Mantenha estas credenciais fora do controle de versão (como o Git) em um projeto real.
$servername = "localhost"; // Geralmente 'localhost' para XAMPP
$username = "root"; // O usuário padrão do MySQL no XAMPP é 'root'
$password = ""; // A senha padrão é vazia no XAMPP (deixe aspas vazias)
$dbname = "beancode_db"; // O nome do seu banco de dados

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