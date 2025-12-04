<?php
session_start();
include 'db.php';

// Verifica se o usuário está logado como aluno
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'student') {
    header("Location: index.php?error=Você precisa estar logado como aluno");
    exit();
}

// Processa a seleção de trilha
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_track'])) {
    $selected_track = $_POST['selected_track'];
    $aluno_id = $_SESSION['user_id'];
    
    // Valida a trilha selecionada
    $trilhas_validas = ['iniciante', 'intermediario', 'avancado'];
    
    if (in_array($selected_track, $trilhas_validas)) {
        // Atualiza a trilha ativa do aluno no banco de dados
        $sql = "UPDATE alunos SET trilha_ativa = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $selected_track, $aluno_id);
        
        if ($stmt->execute()) {
            // Atualiza a sessão
            $_SESSION['trilha_ativa'] = $selected_track;
            
            $stmt->close();
            close_db_connection();
            
            // Redireciona para a página da trilha selecionada
            header("Location: " . $selected_track . ".php");
            exit();
        } else {
            $stmt->close();
            close_db_connection();
            
            header("Location: trilhas.php?error=Erro ao salvar trilha");
            exit();
        }
    } else {
        close_db_connection();
        header("Location: trilhas.php?error=Trilha inválida");
        exit();
    }
} else {
    close_db_connection();
    header("Location: trilhas.php");
    exit();
}
?>
