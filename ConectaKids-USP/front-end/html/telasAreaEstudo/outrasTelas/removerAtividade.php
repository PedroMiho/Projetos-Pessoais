<?php
session_start();
include("../../../../back-end/conexao.php");

if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'profissional') {
  header("Location: ../../telaLogin.php");
  exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $atividade_id = $_POST['atividade_id'];
  $paciente_id = $_POST['paciente_id'];

  // Apagar entregas relacionadas
  $conn->prepare("DELETE FROM entregas_atividades WHERE atividade_id = ?")->execute([$atividade_id]);

  // Apagar atividade
  $stmt = $conn->prepare("DELETE FROM atividades WHERE id = ?");
  $stmt->bind_param("i", $atividade_id);
  $stmt->execute();

  header("Location: corrigirAtividade.php?aluno=$paciente_id&msg=excluido");
  exit();
}
