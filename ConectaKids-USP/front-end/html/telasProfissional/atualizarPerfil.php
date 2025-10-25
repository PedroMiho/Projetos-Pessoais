<?php
session_start();
include("../../../back-end/conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
    header("Location: ../telaLogin.php");
    exit();
}

$id = $_SESSION['usuario_id'];
$tipo = $_SESSION['usuario_tipo'];

// Define a tabela conforme o tipo
$tabela = ($tipo === 'profissional') ? 'profissionais' : 'pacientes';

// Recebe dados do formulário
$nome = trim($_POST['nome'] ?? '');
$sobrenome = trim($_POST['sobrenome'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$especialidade = $_POST['especialidade'] ?? null;
$dificuldade = $_POST['dificuldade'] ?? null;
$descricao = trim($_POST['descricao'] ?? '');
$perfil_publico = isset($_POST['perfil_publico']) ? 1 : 0; // Checkbox

// Caminho base para salvar as imagens
$pastaDestino = "uploads/";
if (!is_dir($pastaDestino)) {
    mkdir($pastaDestino, 0755, true);
}

$foto_perfil = null;

// ====== UPLOAD DA FOTO DE PERFIL ======
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($extensao, $permitidos)) {
        $novoNome = "foto_" . $id . "_" . time() . "." . $extensao;
        $destino = $pastaDestino . $novoNome;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            $foto_perfil = $novoNome;

            // Apaga imagem antiga se existir
            $sqlAntiga = "SELECT foto_perfil FROM $tabela WHERE id = ?";
            $stmtAntiga = $conn->prepare($sqlAntiga);
            $stmtAntiga->bind_param("i", $id);
            $stmtAntiga->execute();
            $resAntiga = $stmtAntiga->get_result()->fetch_assoc();

            if (!empty($resAntiga['foto_perfil']) && file_exists($pastaDestino . $resAntiga['foto_perfil'])) {
                unlink($pastaDestino . $resAntiga['foto_perfil']);
            }
        } else {
            $_SESSION['mensagem'] = "<div class='alert alert-danger'>Erro ao mover o arquivo de imagem.</div>";
            header("Location: telaProfissional.php");
            exit();
        }
    } else {
        $_SESSION['mensagem'] = "<div class='alert alert-danger'>Formato inválido! Use JPG, PNG ou GIF.</div>";
        header("Location: telaProfissional.php");
        exit();
    }
}

// ====== VALIDAÇÃO BÁSICA ======
if (empty($nome) || empty($sobrenome) || empty($descricao) ||
    ($tipo === 'profissional' && empty($especialidade)) ||
    ($tipo === 'paciente' && empty($dificuldade))) {

    $_SESSION['mensagem'] = "<div class='alert alert-warning text-center mt-3'>Preencha todos os campos obrigatórios antes de salvar.</div>";
    header("Location: telaProfissional.php");
    exit();
}

// ====== MONTA O SQL CONFORME O TIPO ======
if ($tipo === 'profissional') {
    $sql = "UPDATE profissionais 
            SET nome = ?, sobrenome = ?, telefone = ?, especialidade = ?, descricao = ?, perfil_publico = ?";

    if ($foto_perfil) $sql .= ", foto_perfil = ?";
    $sql .= " WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($foto_perfil) {
        $stmt->bind_param("sssssssi", $nome, $sobrenome, $telefone, $especialidade, $descricao, $perfil_publico, $foto_perfil, $id);
    } else {
        $stmt->bind_param("ssssssi", $nome, $sobrenome, $telefone, $especialidade, $descricao, $perfil_publico, $id);
    }

} else {
    $sql = "UPDATE pacientes 
            SET nome = ?, sobrenome = ?, telefone = ?, dificuldade = ?, descricao = ?, perfil_publico = ?";

    if ($foto_perfil) $sql .= ", foto_perfil = ?";
    $sql .= " WHERE id = ?";

    $stmt = $conn->prepare($sql);

    if ($foto_perfil) {
        $stmt->bind_param("sssssssi", $nome, $sobrenome, $telefone, $dificuldade, $descricao, $perfil_publico, $foto_perfil, $id);
    } else {
        $stmt->bind_param("ssssssi", $nome, $sobrenome, $telefone, $dificuldade, $descricao, $perfil_publico, $id);
    }
}

// ====== EXECUTA O UPDATE ======
if ($stmt->execute()) {
    $_SESSION['mensagem'] = "<div class='alert alert-success text-center mt-3'>Perfil atualizado com sucesso!</div>";
} else {
    $_SESSION['mensagem'] = "<div class='alert alert-danger text-center mt-3'>Erro ao atualizar o perfil.</div>";
}

// ====== REDIRECIONAMENTO CORRETO ======

header("Location: telaProfissional.php");


exit();
?>
