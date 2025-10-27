<?php
session_start();
include("../back-end/conexao.php");

// 🔹 Função para verificar login via sessão ou cookie
function estaLogado()
{
  global $conn;

  // Se já estiver logado na sessão
  if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_tipo'])) {
    return true;
  }

  // Se houver cookie salvo
  if (isset($_COOKIE['usuario_id']) && isset($_COOKIE['usuario_tipo'])) {
    $usuario_id = $_COOKIE['usuario_id'];
    $usuario_tipo = $_COOKIE['usuario_tipo'];

    // Busca conforme o tipo de usuário
    if ($usuario_tipo === 'profissional') {
      $sql = "SELECT id, nome FROM profissionais WHERE id = ?";
    } else {
      $sql = "SELECT id, nome FROM pacientes WHERE id = ?";
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
      $row = $result->fetch_assoc();
      $_SESSION['usuario_id'] = $row['id'];
      $_SESSION['usuario_nome'] = $row['nome'];
      $_SESSION['usuario_tipo'] = $usuario_tipo;
      return true;
    }
  }

  return false;
}

// 🔹 Define links de navegação conforme o tipo
if (estaLogado()) {
  if ($_SESSION['usuario_tipo'] === 'profissional') {
    $linkEstudos = "html/telasAreaEstudo/areaEstudo.php";
    $linkUsuario = "html/telasAreaEstudo/areaEstudo.php";
  } else {
    $linkEstudos = "html/telasAreaEstudoAluno/areaEstudoAluno.php";
    $linkUsuario = "html/telasAreaEstudoAluno/areaEstudoAluno.php";
  }
} else {
  $linkEstudos = "html/telaLogin.php";
  $linkUsuario = "html/telaLogin.php";
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ConectaKids - Página Inicial</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
</head>

<body>
  <!-- ====== HEADER ====== -->
  <header>
    <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
      <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold fs-5" href="#">ConectaKids</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Alternar navegação">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav w-100">
            <li class="nav-item">
              <a class="nav-link text-white active fs-5" href="html/profissionais.php">Profissionais</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="html/pacientes.php">Pacientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="<?= $linkEstudos; ?>">Área de Estudos</a>
            </li>

            <!-- Perfil/Login à direita -->
            <li class="nav-item ms-auto">
              <?php if (estaLogado()): ?>
                <a class="nav-link text-white d-flex align-items-center fs-5" href="<?= $linkUsuario; ?>">
                  <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['usuario_nome']); ?>
                </a>
              <?php else: ?>
                <a class="nav-link text-white d-flex align-items-center fs-5" href="html/telaLogin.php">
                  <i class="bi bi-person-circle me-1"></i> Login
                </a>
              <?php endif; ?>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- ====== MAIN ====== -->
  <main>
    <!-- Seção de apresentação -->
    <section class="py-5" style="background-color: #efebe9">
      <div class="container text-center">
        <h2 class="fw-bold mb-5" style="color: #3e2723">
          No ConectaKids, seu filho recebe <br />
          cuidado e atenção especial.
        </h2>
        <div class="row g-4">
          <!-- Card 1 -->
          <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
              <img src="imagens/Psicoterapia Infantil.png" class="card-img-top" alt="Psicoterapia Infantil" />
              <div class="card-body" style="background-color: #f5f2f0">
                <h5 class="card-title fw-bold" style="color: #6d4c41">Psicoterapia Infantil</h5>
                <p class="card-text text-muted">
                  Atendimento com escuta ativa da criança e responsáveis,
                  apoio ao desenvolvimento emocional e social, estratégias
                  para comunicação e autoestima.
                </p>
              </div>
            </div>
          </div>
          <!-- Card 2 -->
          <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
              <img src="imagens/Psicopedagogia.png" class="card-img-top" alt="Psicopedagogia" />
              <div class="card-body" style="background-color: #f5f2f0">
                <h5 class="card-title fw-bold" style="color: #6d4c41">Psicopedagogia</h5>
                <p class="card-text text-muted">
                  Identificação e apoio nas dificuldades de aprendizagem,
                  incluindo alfabetização, organização e concentração.
                </p>
              </div>
            </div>
          </div>
          <!-- Card 3 -->
          <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
              <img src="imagens/Neuropsicopedagogia.png" class="card-img-top" alt="Neuropsicopedagogia" />
              <div class="card-body" style="background-color: #f5f2f0">
                <h5 class="card-title fw-bold" style="color: #6d4c41">Neuropsicopedagogia</h5>
                <p class="card-text text-muted">
                  Atua na compreensão do funcionamento cognitivo e emocional,
                  auxiliando no diagnóstico e nas estratégias de aprendizagem.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Abordagens -->
    <section class="py-5" style="background-color: #ede7e3">
      <div class="container text-center">
        <h2 class="fw-bold mb-5" style="color: #3e2723">Principais Abordagens</h2>
        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
              <img src="imagens/Terapia Cognitivo Comportamental.png" class="card-img-top" alt="TCC" />
              <div class="card-body" style="background-color: #f5f2f0">
                <h5 class="card-title fw-bold" style="color: #6d4c41">Terapia Cognitivo-Comportamental</h5>
                <p class="card-text text-muted">
                  Ajuda na modificação de pensamentos e comportamentos,
                  promovendo autoestima e confiança.
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
              <img src="imagens/Psicanálise.png" class="card-img-top" alt="Psicanálise" />
              <div class="card-body" style="background-color: #f5f2f0">
                <h5 class="card-title fw-bold" style="color: #6d4c41">Psicanálise</h5>
                <p class="card-text text-muted">
                  Baseada na escuta da criança e da família, favorecendo o desenvolvimento psíquico e emocional.
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
              <img src="imagens/ABA.png" class="card-img-top" alt="ABA" />
              <div class="card-body" style="background-color: #f5f2f0">
                <h5 class="card-title fw-bold" style="color: #6d4c41">ABA (Análise do Comportamento)</h5>
                <p class="card-text text-muted">
                  Estimula habilidades comunicativas e sociais, promovendo autonomia e inclusão.
                </p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border-0">
              <img src="imagens/1e9cd91e-e49a-471d-88cd-d667e3bd9b60.webp" class="card-img-top" alt="Gestalt-terapia" />
              <div class="card-body" style="background-color: #f5f2f0">
                <h5 class="card-title fw-bold" style="color: #6d4c41">Gestalt-terapia</h5>
                <p class="card-text text-muted">
                  Cria um ambiente de confiança para o crescimento emocional e social da criança.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Escolha -->
    <section class="py-5" style="background-color: #fff8f5">
      <div class="container text-center">
        <h2 class="fw-bold mb-4" style="color: #3e2723">Para quem você procura atendimento?</h2>
        <p class="text-muted mb-5">Escolha abaixo a opção que melhor se encaixa no seu perfil.</p>
        <div class="row g-4 justify-content-center">
          <div class="col-md-6">
            <a href="html/pacientes.php" class="text-decoration-none">
              <div class="p-5 rounded-4 shadow-sm h-100" style="background-color: #f5f2f0;">
                <h3 class="fw-bold" style="color: #6d4c41">Sou Paciente</h3>
                <p class="text-muted">Acesse informações, recursos e profissionais para auxiliar no seu desenvolvimento.</p>
              </div>
            </a>
          </div>
          <div class="col-md-6">
            <a href="html/profissionais.php" class="text-decoration-none">
              <div class="p-5 rounded-4 shadow-sm h-100" style="background-color: #f5f2f0;">
                <h3 class="fw-bold" style="color: #6d4c41">Sou Profissional</h3>
                <p class="text-muted">Cadastre-se e acompanhe seus atendimentos de forma prática.</p>
              </div>
            </a>
          </div>
        </div>
      </div>
    </section>
  </main>

  <!-- ====== FOOTER ====== -->
  <footer class="text-white pt-5 pb-3" style="background-color: #3e2723">
    <div class="container">
      <div class="row justify-content-between align-items-start text-center">
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold">Sobre Nós</h5>
          <p class="small">
            Nosso propósito é conectar crianças e famílias a profissionais especializados, promovendo cuidado,
            desenvolvimento e inclusão de forma acessível e humanizada.
          </p>
        </div>
        <div class="col-md-4 mb-4 d-flex flex-column align-items-center text-center">
          <h5 class="fw-bold">Links Úteis</h5>
          <ul class="list-unstyled small">
            <li><a href="index.php" class="text-white text-decoration-none">Início</a></li>
            <li><a href="html/pacientes.php" class="text-white text-decoration-none">Pacientes</a></li>
            <li><a href="html/profissionais.php" class="text-white text-decoration-none">Profissionais</a></li>
          </ul>
        </div>
        <div class="col-md-4 mb-4">
          <h5 class="fw-bold">Redes Sociais</h5>
          <p class="small">Acompanhe nossas novidades e conteúdos:</p>
          <a href="#" target="_blank" class="text-white me-3"><i class="bi bi-instagram fs-4"></i></a>
          <a href="#" target="_blank" class="text-white me-3"><i class="bi bi-facebook fs-4"></i></a>
          <a href="#" target="_blank" class="text-white"><i class="bi bi-whatsapp fs-4"></i></a>
        </div>
      </div>
      <hr class="border-light" />
      <div class="text-center small">
        <p class="mb-0">© 2025 Espaço Escuta - Todos os direitos reservados.</p>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>

</html>
