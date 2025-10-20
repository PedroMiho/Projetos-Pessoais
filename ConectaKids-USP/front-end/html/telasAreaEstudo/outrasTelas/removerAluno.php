<?php
session_start();
include("../../../../back-end/conexao.php");

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'profissional') {
    header("Location: ../../telaLogin.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paciente_id'])) {
    $profissional_id = $_SESSION['usuario_id'];
    $paciente_id = intval($_POST['paciente_id']);

    $sql = "DELETE FROM vinculos_profissionais_pacientes WHERE profissional_id = ? AND paciente_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $profissional_id, $paciente_id);

    if ($stmt->execute()) {
        header("Location: cadastrarAluno.php?msg=removido");
        exit();
    } else {
        echo "<script>alert('Erro ao remover o vínculo.'); window.location.href='cadastrarAluno.php';</script>";
    }
}
?>
