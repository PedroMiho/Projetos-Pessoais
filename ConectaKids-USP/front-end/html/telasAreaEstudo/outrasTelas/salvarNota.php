<?php
session_start();
include("../../../../back-end/conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $entrega_id = $_POST['entrega_id'];
    $nota = $_POST['nota'];

    $sql = "UPDATE entregas_atividades SET nota = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("di", $nota, $entrega_id);
    $stmt->execute();

    $_SESSION['mensagem'] = "<div class='alert alert-success text-center'>Nota salva com sucesso!</div>";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit();
}
?>
