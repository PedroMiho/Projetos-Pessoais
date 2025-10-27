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
  $nome = $_POST['nome_atividade'];
  $descricao = $_POST['descricao'];
  $dataEncerramento = $_POST['data_encerramento'];

  // Atualiza dados
  $sql = "UPDATE atividades SET nome_atividade=?, descricao=?, data_encerramento=? WHERE id=?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("sssi", $nome, $descricao, $dataEncerramento, $atividade_id);
  $stmt->execute();

  // Atualiza PDF se enviado
  if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] === 0) {
    $pasta = "../../telasProfissional/uploads/";
    if (!is_dir($pasta)) mkdir($pasta, 0755, true);
    $nomeArquivo = "atividade_" . time() . "_" . basename($_FILES['arquivo_pdf']['name']);
    $caminho = $pasta . $nomeArquivo;
    move_uploaded_file($_FILES['arquivo_pdf']['tmp_name'], $caminho);

    $stmt2 = $conn->prepare("UPDATE atividades SET arquivo_pdf=? WHERE id=?");
    $stmt2->bind_param("si", $caminho, $atividade_id);
    $stmt2->execute();
  }

  header("Location: corrigirAtividade.php?aluno=$paciente_id&msg=editado");
  exit();
}
