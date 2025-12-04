<?php
// db.php - Configuração centralizada do banco de dados
// ATENÇÃO: Mantenha estas credenciais fora do controle de versão (como o Git) em um projeto real.

// Ativa erros para debug (desative em produção)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Configurações do banco
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_USER', 'if0_40593316');
define('DB_PASS', 'hEF5guZ3oop6ty2');
define('DB_NAME', 'if0_40593316_db_beancode');

// Cria a conexão
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Checa a conexão
if ($conn->connect_error) {
    error_log("Falha na conexão mágica: " . $conn->connect_error);
    die("Falha na conexão com o banco de dados. Por favor, tente novamente mais tarde.");
}

// Define o charset para evitar problemas de acentuação
$conn->set_charset("utf8mb4");

// Função auxiliar para fechar conexão de forma segura
function close_db_connection() {
    global $conn;
    if (isset($conn) && $conn instanceof mysqli) {
        $conn->close();
    }
}
?>