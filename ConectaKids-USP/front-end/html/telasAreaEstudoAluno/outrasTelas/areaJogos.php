<?php
session_start();
include("../../../../back-end/conexao.php");

// Função para verificar login
function estaLogado()
{
  return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_tipo']);
}

// Verifica se o usuário é paciente
if (!estaLogado() || $_SESSION['usuario_tipo'] !== 'paciente') {
  header("Location: ../../telaLogin.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Área de Jogos - ConectaKids</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    body {
      background-color: #fff8f5;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    main {
      flex: 1;
    }
    footer {
      background-color: #3e2723;
      color: white;
      padding: 1rem 0;
      text-align: center;
      margin-top: auto;
    }
    .card-game {
      background-color: #f5f2f0;
      border: none;
      border-radius: 16px;
      transition: transform 0.2s, box-shadow 0.2s;
      cursor: pointer;
    }
    .card-game:hover {
      transform: translateY(-5px);
      box-shadow: 0px 4px 15px rgba(0,0,0,0.15);
    }
    .card-icon {
      width: 80px;
      height: 80px;
      background-color: #6d4c41;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 0 auto 15px;
      color: white;
      font-size: 2rem;
    }
  </style>
</head>

<body>
  <!-- Header -->
  <header>
    <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
      <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold fs-5" href="../../../index.php">ConectaKids</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav w-100">
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="../../profissionais.php">Profissionais</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="../../pacientes.php">Pacientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="../../telasAreaEstudoAluno/areaEstudoAluno.php">Área de Estudos</a>
            </li>
            <li class="nav-item ms-auto">
              <a class="nav-link text-white d-flex align-items-center fs-5" href="../../telasAreaEstudoAluno/areaEstudoAluno.php">
                <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['usuario_nome']); ?>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- Conteúdo -->
  <main class="container py-5">
    <h2 class="text-center fw-bold mb-3" style="color:#3e2723">
      <i class="bi bi-controller me-2"></i>Área de Jogos Educativos
    </h2>
    <p class="text-center text-muted mb-5">
      Explore jogos interativos que ajudam no seu aprendizado e desenvolvimento cognitivo.
    </p>

    <div class="row g-4 justify-content-center">
      <!-- Jogo 1 -->
      <div class="col-md-4">
        <div class="card card-game text-center p-4 h-100" onclick="window.location.href='../jogosEducativos/alfabeto/alfabeto.html'">
          <div class="card-icon"><i class="bi bi-alphabet"></i></div>
          <h5 class="fw-bold" style="color:#3e2723">Aprenda as Letras</h5>
          <p class="text-muted mb-0">Passe o mouse sobre as letras e descubra palavras que começam com cada uma delas!</p>
        </div>
      </div>

      <!-- Jogo 2 -->
      <div class="col-md-4">
        <div class="card card-game text-center p-4 h-100" onclick="window.location.href='jogos/memoria.html'">
          <div class="card-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
          <h5 class="fw-bold" style="color:#3e2723">Jogo da Memória</h5>
          <p class="text-muted mb-0">Treine sua memória encontrando os pares corretos o mais rápido possível!</p>
        </div>
      </div>

      <!-- Jogo 3 -->
      <div class="col-md-4">
        <div class="card card-game text-center p-4 h-100" onclick="window.location.href='jogos/numeros.html'">
          <div class="card-icon"><i class="bi bi-123"></i></div>
          <h5 class="fw-bold" style="color:#3e2723">Aprendendo Números</h5>
          <p class="text-muted mb-0">Identifique números, conte objetos e aprenda brincando com a matemática!</p>
        </div>
      </div>

      <!-- Jogo 4 -->
      <div class="col-md-4">
        <div class="card card-game text-center p-4 h-100" onclick="window.location.href='jogos/formas.html'">
          <div class="card-icon"><i class="bi bi-square"></i></div>
          <h5 class="fw-bold" style="color:#3e2723">Formas e Cores</h5>
          <p class="text-muted mb-0">Reconheça formas geométricas e cores através de desafios interativos.</p>
        </div>
      </div>

      <!-- Jogo 5 -->
      <div class="col-md-4">
        <div class="card card-game text-center p-4 h-100" onclick="window.location.href='../jogosEducativos/palavra/palavra.html'">
          <div class="card-icon"><i class="bi bi-chat-text"></i></div>
          <h5 class="fw-bold" style="color:#3e2723">Monte a Palavra</h5>
          <p class="text-muted mb-0">Arraste as letras para formar palavras corretas e melhore sua ortografia!</p>
        </div>
      </div>

      <!-- Jogo 6 -->
      <div class="col-md-4">
        <div class="card card-game text-center p-4 h-100" onclick="window.location.href='../jogosEducativos/palavra/palavra2.html'">
          <div class="card-icon"><i class="bi bi-chat-right-dots"></i></div>
          <h5 class="fw-bold" style="color:#3e2723">Monte a Palavra 2.0</h5>
          <p class="text-muted mb-0">Arraste as letras para formar palavras corretas e melhore sua ortografia!</p>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="container small">
      <p class="mb-0">© 2025 ConectaKids - Todos os direitos reservados.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
