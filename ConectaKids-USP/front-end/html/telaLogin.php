<?php
session_start();

// Recupera e limpa a mensagem da sessão
$mensagem = "";
if (isset($_SESSION['mensagem'])) {
  $mensagem = $_SESSION['mensagem'];
  unset($_SESSION['mensagem']); // limpa para não repetir
}

// Lê cookies (caso o usuário tenha marcado “lembrar-me”)
$valueEmail = $_COOKIE['email'] ?? '';
$valueSenha = $_COOKIE['senha'] ?? '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tela de Login - ConectaKids</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
  <style>
    body {
      background-color: #efebe9;
      overflow: hidden;
    }

    .mensagem {
      text-align: center;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 8px;
      font-weight: 500;
    }

    .mensagem.erro,
    .alert-danger {
      background-color: #ffcdd2;
      color: #b71c1c;
      border: none;
    }

    .mensagem.sucesso,
    .alert-success {
      background-color: #c8e6c9;
      color: #256029;
      border: none;
    }
  </style>
</head>

<body>
  <!-- Header -->
  <header>
    <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
      <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold fs-5" href="../index.php">ConectaKids</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav w-100">
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="profissionais.php">Profissionais</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="pacientes.php">Pacientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="telaLogin.php">Área de Estudos</a>
            </li>
            <li class="nav-item ms-auto">
              <a class="nav-link text-white d-flex align-items-center fs-5 active" href="telaLogin.php">
                <i class="bi bi-person-circle me-1"></i> Login
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- Main -->
  <main class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card shadow-sm p-4 rounded-4 w-100" style="max-width: 400px; background-color: #f5f2f0">
      <div class="text-center mb-4">
        <i class="bi bi-person-circle" style="font-size: 4rem; color: #6d4c41"></i>
        <h3 class="fw-bold mt-2" style="color: #3e2723">Login</h3>
      </div>

      <!-- Mensagem PHP -->
      <?php if (!empty($mensagem)) echo $mensagem; ?>

      <!-- Formulário de Login -->
      <form action="login.php" method="post">
        <div class="mb-3">
          <label for="email" class="form-label fw-semibold">E-mail</label>
          <input type="email" class="form-control" id="email" name="email" placeholder="Digite seu e-mail"
            value="<?= htmlspecialchars($valueEmail) ?>" required />
        </div>

        <div class="mb-3">
          <label for="senha" class="form-label fw-semibold">Senha</label>
          <input type="password" class="form-control" name="senha" id="senha" placeholder="Digite sua senha"
            value="<?= htmlspecialchars($valueSenha) ?>" required />
        </div>

        <div class="form-check mb-3">
          <input class="form-check-input" type="checkbox" name="lembrar" id="lembrar">
          <label class="form-check-label" for="lembrar">Manter-me conectado</label>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn text-white fw-semibold" style="background-color: #6d4c41">
            Entrar
          </button>
          <a href="#" class="btn btn-outline-secondary fw-semibold">
            Esqueci minha senha
          </a>
        </div>
      </form>

      <!-- Link de cadastro -->
      <div class="text-center mt-4">
        <p class="mb-1">Ainda não tem conta?</p>
        <a href="paginaCadastro.php" class="fw-bold text-decoration-none" style="color: #6d4c41">
          Criar uma conta
        </a>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
