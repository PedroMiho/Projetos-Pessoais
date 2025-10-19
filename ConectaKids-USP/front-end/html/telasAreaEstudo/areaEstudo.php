<?php
session_start();
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
  header("Location: ../telaLogin.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Painel do Profissional - ConectaKids</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    /* Estrutura geral da página */
    html, body {
      height: 100%;
      margin: 0;
    }

    body {
      display: flex;
      flex-direction: column;
      background-color: #fff8f5;
    }

    main {
      flex: 1; /* ocupa o espaço restante empurrando o footer para baixo */
      padding-bottom: 40px; /* evita que o conteúdo encoste no footer */
    }

    /* Estilo das cards */
    .card-opcao {
      transition: all 0.3s ease-in-out;
      border: none;
      border-radius: 15px;
      background-color: #f5f2f0;
      cursor: pointer;
    }

    .card-opcao:hover {
      transform: translateY(-5px);
      box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
    }

    .icon-container {
      background-color: #6d4c41;
      color: #fff;
      width: 70px;
      height: 70px;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 2rem;
      margin: 0 auto 15px;
    }

    h5 {
      color: #3e2723;
      font-weight: bold;
    }

    p {
      color: #4e342e;
    }

    /* Footer */
    footer {
      background-color: #3e2723;
      color: white;
      padding-top: 20px;
      padding-bottom: 20px;
    }
    footer  p {
      color: white;
    }
    footer  h5 {
      color: white;
    }

    footer a {
      color: white;
      text-decoration: none;
    }

    footer a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
    <div class="container-fluid">
      <a class="navbar-brand text-white fw-bold fs-5" href="../../index.php">ConectaKids</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav w-100">
          <li class="nav-item"><a class="nav-link text-white fs-5" href="../profissionais.php">Profissionais</a></li>
          <li class="nav-item"><a class="nav-link text-white fs-5" href="../pacientes.php">Pacientes</a></li>
          <li class="nav-item"><a class="nav-link text-white fs-5" href="#">Área de Estudos</a></li>
          <li class="nav-item ms-auto">
            <a class="nav-link text-white fs-5" href="logout.php">
              <i class="bi bi-box-arrow-right me-1"></i> Sair
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Conteúdo principal -->
  <main class="py-5">
    <div class="container text-center">
      <h2 class="fw-bold mb-4" style="color: #3e2723;">Painel do Profissional</h2>
      <p class="mb-5" style="color: #5d4037;">Gerencie suas atividades, alunos e perfil de forma simples e intuitiva.</p>

      <div class="row g-4 justify-content-center">

        <!-- Cadastrar Atividade -->
        <div class="col-md-5 col-lg-3">
          <div class="card card-opcao p-4 h-100" onclick="window.location.href='cadastrarAtividade.php'">
            <div class="icon-container"><i class="bi bi-journal-plus"></i></div>
            <h5>Cadastrar Atividade</h5>
            <p>Crie novas atividades para seus alunos de maneira prática e rápida.</p>
          </div>
        </div>

        <!-- Cadastrar Aluno -->
        <div class="col-md-5 col-lg-3">
          <div class="card card-opcao p-4 h-100" onclick="window.location.href='cadastrarAluno.php'">
            <div class="icon-container"><i class="bi bi-person-plus"></i></div>
            <h5>Cadastrar Aluno</h5>
            <p>Adicione um novo aluno à sua lista para acompanhar o desenvolvimento.</p>
          </div>
        </div>

        <!-- Corrigir Atividade -->
        <div class="col-md-5 col-lg-3">
          <div class="card card-opcao p-4 h-100" onclick="window.location.href='corrigirAtividade.php'">
            <div class="icon-container"><i class="bi bi-pencil-square"></i></div>
            <h5>Corrigir Atividade</h5>
            <p>Visualize e corrija as atividades enviadas pelos seus alunos.</p>
          </div>
        </div>

        <!-- Acessar Perfil -->
        <div class="col-md-5 col-lg-3">
          <div class="card card-opcao p-4 h-100" onclick="window.location.href='../telasProfissional/telaProfissional.php'">
            <div class="icon-container"><i class="bi bi-person-circle"></i></div>
            <h5>Acessar Perfil</h5>
            <p>Veja e edite suas informações pessoais e profissionais.</p>
          </div>
        </div>

      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-center">
    <div class="container">
      <div class="row justify-content-between align-items-start text-center">
        <!-- Sobre -->
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold">Sobre Nós</h5>
          <p class="small">
            Nosso propósito é conectar crianças e famílias a profissionais
            especializados, promovendo cuidado, desenvolvimento e inclusão de
            forma acessível e humanizada.
          </p>
        </div>

        <!-- Links Úteis -->
        <div class="col-md-4 mb-4 d-flex flex-column align-items-center text-center">
          <h5 class="fw-bold">Links Úteis</h5>
          <ul class="list-unstyled small">
            <li><a href="index.html">Início</a></li>
            <li><a href="html/pacientes.php">Pacientes</a></li>
            <li><a href="html/profissionais.php">Profissionais</a></li>
          </ul>
        </div>

        <!-- Redes Sociais -->
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold">Redes Sociais</h5>
          <p class="small">Acompanhe nossas novidades e conteúdos:</p>
          <a href="https://instagram.com/seuInstagram" target="_blank" class="me-3"><i class="bi bi-instagram fs-4"></i></a>
          <a href="https://facebook.com/seuFacebook" target="_blank" class="me-3"><i class="bi bi-facebook fs-4"></i></a>
          <a href="https://wa.me/seuNumero" target="_blank"><i class="bi bi-whatsapp fs-4"></i></a>
        </div>
      </div>

      <hr class="border-light" />

      <div class="text-center small">
        <p class="mb-0">© 2025 Espaço Escuta - Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
