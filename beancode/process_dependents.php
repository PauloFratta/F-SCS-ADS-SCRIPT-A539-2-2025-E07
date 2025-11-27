<?php
// process_dependents.php (CÓDIGO CORRIGIDO PARA A TABELA 'alunos')
session_start();
include 'db.php';

// Verifica se o responsável está logado e tem o tipo correto
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'responsible' || !isset($_POST['dependents'])) {
    header("Location: login.php"); 
    exit();
}

$responsible_id = $_SESSION['user_id'];
$dependents_data = $_POST['dependents'];
$successful_count = 0;
$error_count = 0;

// Prepara a consulta para inserir o aluno.
// ATENÇÃO: A tabela correta é 'alunos' e estamos usando as colunas essenciais:
// responsavel_id, nome_user (apelido/nome de exibição), trilha_ativa e senha_hash.
$sql = "INSERT INTO alunos (responsavel_id, nome_user, trilha_ativa, senha_hash) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    foreach ($dependents_data as $dependent) {
        $name = $conn->real_escape_string($dependent['name'] ?? ''); // Nome do aluno / nome_user
        $course = $conn->real_escape_string($dependent['course'] ?? 'iniciante'); // trilha_ativa
        $password = $dependent['password'] ?? '';
        $repeat_password = $dependent['repeat_password'] ?? '';

        if (empty($name) || empty($password) || $password !== $repeat_password) {
            $error_count++;
            continue; 
        }
        
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Binding: responsavel_id (int), nome_user (string), trilha_ativa (string), senha_hash (string)
        $stmt->bind_param("isss", $responsible_id, $name, $course, $password_hash);

        if ($stmt->execute()) {
            $successful_count++;
        } else {
            $error_count++;
        }
    }
    $stmt->close();
}

$conn->close();

// Redireciona para o novo painel do responsável após o cadastro
header("Location: dashboard_responsavel.php");
exit();
?>