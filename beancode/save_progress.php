<?php
// Inicia output buffering para evitar saída indesejada antes do JSON
ob_start();

session_start();
include 'db.php';

// Verifica se o aluno está logado
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    ob_clean(); // Limpa qualquer output anterior
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

$aluno_id = $_SESSION['user_id'];
$licao_id = isset($_POST['licao_id']) ? (int)$_POST['licao_id'] : 0;

if ($licao_id <= 0) {
    ob_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Lição inválida']);
    exit();
}

// Verifica se já existe progresso
$sql_check = "SELECT concluida FROM progresso_licoes WHERE aluno_id = ? AND licao_id = ?";
$stmt = $conn->prepare($sql_check);
$stmt->bind_param("ii", $aluno_id, $licao_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Atualiza se já existe
    $stmt->close();
    $sql_update = "UPDATE progresso_licoes SET concluida = 1, data_conclusao = NOW() WHERE aluno_id = ? AND licao_id = ?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("ii", $aluno_id, $licao_id);
    $stmt->execute();
} else {
    // Insere novo progresso
    $stmt->close();
    $sql_insert = "INSERT INTO progresso_licoes (aluno_id, licao_id, concluida, data_conclusao) VALUES (?, ?, 1, NOW())";
    $stmt = $conn->prepare($sql_insert);
    $stmt->bind_param("ii", $aluno_id, $licao_id);
    $stmt->execute();
}

$stmt->close();

// Buscar informações da lição e do aluno para criar notificação
try {
    $sql_info = "SELECT l.titulo, a.responsavel_id, a.nome_user, m.nome as modulo_nome, cam.nome as caminho_nome
                 FROM licoes l
                 INNER JOIN modulos m ON l.modulo_id = m.id
                 INNER JOIN cursos c ON m.curso_id = c.id
                 INNER JOIN caminhos cam ON c.caminho_id = cam.id
                 INNER JOIN alunos a ON a.id = ?
                 WHERE l.id = ?";
    $stmt_info = $conn->prepare($sql_info);
    $stmt_info->bind_param("ii", $aluno_id, $licao_id);
    $stmt_info->execute();
    $result_info = $stmt_info->get_result();

    if ($row_info = $result_info->fetch_assoc()) {
        $responsavel_id = $row_info['responsavel_id'];
        $licao_titulo = $row_info['titulo'];
        $caminho_nome = $row_info['caminho_nome'];
        $modulo_nome = $row_info['modulo_nome'];
        
        // Criar mensagem de notificação
        $mensagem = "concluiu a lição \"" . $licao_titulo . "\" do módulo \"" . $modulo_nome . "\" na trilha " . $caminho_nome . "! 🎉";
        
        // Inserir notificação
        $sql_notif = "INSERT INTO notificacoes (responsavel_id, aluno_id, mensagem, lida) VALUES (?, ?, ?, 0)";
        $stmt_notif = $conn->prepare($sql_notif);
        $stmt_notif->bind_param("iis", $responsavel_id, $aluno_id, $mensagem);
        $stmt_notif->execute();
        $stmt_notif->close();
    }
    $stmt_info->close();
} catch (Exception $e) {
    // Silenciosamente ignora erro de notificação para não quebrar o progresso
    error_log("Erro ao criar notificação: " . $e->getMessage());
}

close_db_connection();

// Limpa qualquer output anterior e garante que apenas JSON seja enviado
ob_clean();
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => 'Progresso salvo!']);
ob_end_flush();
?>
