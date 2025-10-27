<?php
session_start();
include("../../back-end/conexao.php");

// ===== CONFIGURAÇÕES =====
$tempoExpiracaoSessao = 30 * 60; // 30 minutos

// ===== LIMPA SESSÃO ANTIGA =====
if (!empty($_SESSION)) {
    session_unset();
    session_destroy();
    session_start();
}

// ===== VERIFICA SE USUÁRIO JÁ ESTÁ LOGADO =====
if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_tipo'])) {
    if ($_SESSION['usuario_tipo'] === 'profissional') {
        header("Location: telasAreaEstudo/areaEstudo.php");
    } else {
        header("Location: telasAreaEstudoAluno/areaEstudoAluno.php");
    }
    exit();
}

// ===== PROCESSA LOGIN =====
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    // ===== FUNÇÃO PARA VERIFICAR USUÁRIO (sem hash) =====
    function verificarUsuario($conn, $tabela, $email, $senha)
    {
        $sql = "SELECT id, nome FROM $tabela WHERE email = ? AND senha = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $senha);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc() ?: false;
    }

    // ===== 1️⃣ Verifica na tabela de profissionais =====
    $user = verificarUsuario($conn, "profissionais", $email, $senha);
    if ($user) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_tipo'] = 'profissional';
        $_SESSION['ultimo_acesso'] = time();

        // Salva cookie se o usuário marcou "lembrar"
        if (isset($_POST['lembrar'])) {
            setcookie("usuario_id", $user['id'], time() + (7 * 24 * 60 * 60), "/", "", true, true);
            setcookie("usuario_tipo", "profissional", time() + (7 * 24 * 60 * 60), "/", "", true, true);
        }

        header("Location: telasAreaEstudo/areaEstudo.php");
        exit();
    }

    // ===== 2️⃣ Verifica na tabela de pacientes =====
    $user = verificarUsuario($conn, "pacientes", $email, $senha);
    if ($user) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_tipo'] = 'paciente';
        $_SESSION['ultimo_acesso'] = time();

        if (isset($_POST['lembrar'])) {
            setcookie("usuario_id", $user['id'], time() + (7 * 24 * 60 * 60), "/", "", true, true);
            setcookie("usuario_tipo", "paciente", time() + (7 * 24 * 60 * 60), "/", "", true, true);
        }

        header("Location: telasAreaEstudoAluno/areaEstudoAluno.php");
        exit();
    }

    // ===== 3️⃣ Nenhum usuário encontrado =====
    $_SESSION['mensagem'] = "<div class='alert alert-danger text-center mt-3'>E-mail ou senha incorretos.</div>";
    header("Location: telaLogin.php");
    exit();
}
?>
