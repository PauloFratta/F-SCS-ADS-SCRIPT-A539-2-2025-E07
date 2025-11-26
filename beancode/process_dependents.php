<?php
session_start();
include 'db.php';

// Verifica se o responsável está logado (Simulação - em um ambiente real viria da SESSION)
$responsible_id = $_POST['responsible_id'] ?? ($_SESSION['user_id'] ?? null); 

if (!$responsible_id || !isset($_POST['dependents'])) {
    // Redireciona ou mostra erro se não houver responsável ou dados.
    header("Location: dashboard.php"); // Redireciona se a sessão do responsável não for encontrada.
    exit();
}

$dependents_data = $_POST['dependents'];
$successful_count = 0;
$error_count = 0;

// Prepara a consulta para inserir o dependente.
// Assumimos que a tabela é 'dependentes'.
$sql = "INSERT INTO dependentes (responsavel_id, nome_completo, apelido, idade, trilha_inicial, senha_hash) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    foreach ($dependents_data as $dependent) {
        $name = $conn->real_escape_string($dependent['name']);
        $nickname = $conn->real_escape_string($dependent['nickname'] ?? $name);
        $age = (int)$dependent['age'];
        $course = $conn->real_escape_string($dependent['course']);
        $password = $dependent['password'];
        $repeat_password = $dependent['repeat_password'];

        if ($password !== $repeat_password) {
            $error_count++;
            continue; // Pula este dependente
        }
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt->bind_param("ississ", $responsible_id, $name, $nickname, $age, $course, $password_hash);

        if ($stmt->execute()) {
            $successful_count++;
        } else {
            $error_count++;
        }
    }
    $stmt->close();
}

$conn->close();

// Redireciona para o painel de controle do responsável após o processamento
$_SESSION['dependents_status'] = [
    'success' => $successful_count,
    'error' => $error_count
];
header("Location: dashboard.php");
exit();
?>