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
$nome = $_POST['nome'] ?? '';
$sobrenome = $_POST['sobrenome'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$especialidade = $_POST['especialidade'] ?? null;
$descricao = $_POST['descricao'] ?? null;
$dificuldade = $_POST['dificuldade'] ?? null;

// Caminho base para salvar as imagens
$pastaDestino = "uploads/";
if (!is_dir($pastaDestino)) {
    mkdir($pastaDestino, 0755, true);
}

// Variável que vai guardar o nome do novo arquivo (se existir upload)
$foto_perfil = null;

// 🔹 Se o usuário enviou uma nova foto
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
    $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
    $permitidos = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($extensao, $permitidos)) {
        $novoNome = "foto_" . $id . "_" . time() . "." . $extensao;
        $destino = $pastaDestino . $novoNome;

        // Move o arquivo para a pasta de destino
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            $foto_perfil = $novoNome;

            // Remove imagem antiga (se existir)
            $sqlAntiga = "SELECT foto_perfil FROM $tabela WHERE id = ?";
            $stmtAntiga = $conn->prepare($sqlAntiga);
            $stmtAntiga->bind_param("i", $id);
            $stmtAntiga->execute();
            $resAntiga = $stmtAntiga->get_result()->fetch_assoc();

            if (!empty($resAntiga['foto_perfil']) && file_exists($pastaDestino . $resAntiga['foto_perfil'])) {
                unlink($pastaDestino . $resAntiga['foto_perfil']);
            }
        } else {
            echo "<div class='alert alert-danger'>Erro ao mover o arquivo!</div>";
            exit();
        }
    } else {
        echo "<div class='alert alert-danger'>Formato de imagem não permitido! Envie JPG, PNG ou GIF.</div>";
        exit();
    }
}

// 🔹 Monta o SQL de atualização conforme o tipo de usuário
if ($tipo === 'profissional') {
    $sql = "UPDATE profissionais 
            SET nome = ?, sobrenome = ?, telefone = ?, especialidade = ?, descricao = ?";

    if ($foto_perfil) {
        $sql .= ", foto_perfil = ?";
    }

    $sql .= " WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($foto_perfil) {
        $stmt->bind_param("ssssssi", $nome, $sobrenome, $telefone, $especialidade, $descricao, $foto_perfil, $id);
    } else {
        $stmt->bind_param("sssssi", $nome, $sobrenome, $telefone, $especialidade, $descricao, $id);
    }

} else { // Paciente
    $sql = "UPDATE pacientes 
            SET nome = ?, sobrenome = ?, telefone = ?, dificuldade = ?, descricao = ?";

    if ($foto_perfil) {
        $sql .= ", foto_perfil = ?";
    }

    $sql .= " WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($foto_perfil) {
        $stmt->bind_param("ssssssi", $nome, $sobrenome, $telefone, $dificuldade, $descricao, $foto_perfil, $id);
    } else {
        $stmt->bind_param("sssssi", $nome, $sobrenome, $telefone, $dificuldade, $descricao, $id);
    }
}

// Executa o update
if ($stmt->execute()) {
    $_SESSION['mensagem'] = "<div class='alert alert-success text-center mt-3'>Perfil atualizado com sucesso!</div>";
} else {
    $_SESSION['mensagem'] = "<div class='alert alert-danger text-center mt-3'>Erro ao atualizar o perfil.</div>";
}

// Redireciona de volta para o perfil
header("Location: telaProfissional.php");
exit();
?>
