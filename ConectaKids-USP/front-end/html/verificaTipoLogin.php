<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
    header("Location: ../telaLogin.php");
    exit();
}

// Redireciona conforme o tipo de usuário
if ($_SESSION['usuario_tipo'] === 'profissional') {
    header("Location: html/telasAreaEstudo/areaEstudo.php"); // página que você já tem
    exit();
} else {
    header("Location: html/telasAreaEstudoAluno/areaEstudoAluno.php"); // página do aluno
    exit();
}
?>
