<?php
session_start();

// Limpa todas as variáveis de sessão
$_SESSION = [];

// Destroi a sessão
session_destroy();

// Apaga cookies relacionados ao login
if (isset($_COOKIE['usuario_id'])) {
    setcookie('usuario_id', '', time() - 3600, '/'); // expira imediatamente
}
if (isset($_COOKIE['usuario_nome'])) {
    setcookie('usuario_nome', '', time() - 3600, '/');
}

// Redireciona para a tela de login
header("Location: ../telaLogin.php");
exit();
?>
