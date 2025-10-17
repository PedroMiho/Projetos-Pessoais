<?php
session_start();
include("../../back-end/conexao.php");

// ====== CONFIGURAÇÕES ======
$tempoExpiracaoSessao = 30 * 60; // 30 minutos

// ====== VERIFICA SE USUÁRIO JÁ ESTÁ LOGADO ======
if (isset($_SESSION['usuario_id'])) {
    header("Location: telasProfissional/telaProfissional.php");
    exit();
}

// ====== VERIFICA COOKIES (MANTER CONECTADO) ======
if (isset($_COOKIE['email']) && isset($_COOKIE['senha'])) {
    $_POST['email'] = $_COOKIE['email'];
    $_POST['senha'] = $_COOKIE['senha'];
    $_SERVER["REQUEST_METHOD"] = "POST"; // força o processamento abaixo
}

// ====== PROCESSA LOGIN ======
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // 🔍 1️⃣ Verifica na tabela de profissionais
    $sqlProf = "SELECT id, nome, 'profissional' AS tipo FROM profissionais WHERE email = ? AND senha = ?";
    $stmtProf = $conn->prepare($sqlProf);
    $stmtProf->bind_param("ss", $email, $senha);
    $stmtProf->execute();
    $resultProf = $stmtProf->get_result();

    if ($resultProf->num_rows > 0) {
        $user = $resultProf->fetch_assoc();
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_tipo'] = $user['tipo'];
        $_SESSION['ultimo_acesso'] = time(); // salva o horário do login

        // Se marcar "lembrar"
        if (isset($_POST['lembrar'])) {
            setcookie("email", $email, time() + (7 * 24 * 60 * 60), "/");
            setcookie("senha", $senha, time() + (7 * 24 * 60 * 60), "/");
        }

        header("Location: telasProfissional/telaProfissional.php");
        exit();
    }

    // 🔍 2️⃣ Verifica na tabela de pacientes
    $sqlPac = "SELECT id, nome, 'paciente' AS tipo FROM pacientes WHERE email = ? AND senha = ?";
    $stmtPac = $conn->prepare($sqlPac);
    $stmtPac->bind_param("ss", $email, $senha);
    $stmtPac->execute();
    $resultPac = $stmtPac->get_result();

    if ($resultPac->num_rows > 0) {
        $user = $resultPac->fetch_assoc();
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_tipo'] = $user['tipo'];
        $_SESSION['ultimo_acesso'] = time();

        if (isset($_POST['lembrar'])) {
            setcookie("email", $email, time() + (7 * 24 * 60 * 60), "/");
            setcookie("senha", $senha, time() + (7 * 24 * 60 * 60), "/");
        }

        header("Location: telasProfissional/telaProfissional.php");
        exit();
    }

    // ❌ 3️⃣ Se não encontrou em nenhuma tabela
    $_SESSION['mensagem'] = "<div class='alert alert-danger text-center mt-3'>E-mail ou senha incorretos.</div>";
    header("Location: telaLogin.php");
    exit();
}
?>
