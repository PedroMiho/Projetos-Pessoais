<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pacientes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
  <style>
    html,
    body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
    }

    main {
      flex: 1;
    }

    body {
      background-color: #ede7e3;
    }

    .card-custom {
      border: none;
      border-radius: 15px;
      overflow: hidden;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
      margin-bottom: 20px;
      display: flex;
      flex-direction: column;
      flex-wrap: wrap;
    }

    .card-left {
      background-color: #efebe9;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: 20px;
      text-align: center;
      flex: 1;
    }

    .card-left img {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      border: 5px solid #fff;
      margin-bottom: 15px;
    }

    .card-right {
      background-color: #ffffff;
      color: #3c405c;
      display: flex;
      flex-direction: column;
      justify-content: center;
      padding: 30px;
      flex: 2;
    }

    .card-right h5 {
      font-weight: bold;
      margin-bottom: 10px;
    }

    .info-item {
      margin-bottom: 8px;
    }

    @media (min-width: 768px) {
      .card-custom {
        flex-direction: row;
      }
    }
  </style>
</head>

<body>

  <!-- Header -->
  <header>
    <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
      <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold fs-5" href="../index.html">ConectaKids</a>
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
              <a class="nav-link text-white active fs-5" aria-current="page" href="pacientes.php">Pacientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="telaLogin.php">Área de Estudos</a>
            </li>
            <li class="nav-item ms-auto">
              <a class="nav-link text-white d-flex align-items-center fs-5" href="telaLogin.php">
                <i class="bi bi-person-circle me-1"></i> Login
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- Main -->
  <main>
    <div class="container py-5">
      <h2 class="text-center mb-4">Pacientes</h2>

      <?php
      try {
        include("../../back-end/conexao.php");

        $sql = "SELECT * FROM pacientes";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
          $stmt->execute();
          $resultado = $stmt->get_result();

          if ($resultado->num_rows > 0) {
            while ($row = $resultado->fetch_assoc()) {
              $telefone = $row['telefone']; // Ex: 22222222222

              // Formata para (22) 22222-2222
              $telefoneFormatado = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
              echo "
                  <div class='card-custom'>
                    <div class='card-left'>
                      <img src='' alt='Coletar'>
                      <h5>{$row['nome']}</h5>
                      <p>Coletar</p>
                    </div>
                    <div class='card-right'>
                      <p class='info-item'><strong>Telefone:</strong> {$telefoneFormatado} </p>
                      <p class='info-item'><strong>E-mail:</strong> {$row['email']}</p>
                      <p class='info-item'><strong>Especialização:</strong> Coletar</p>
                    </div>
                  </div>
                
                ";
            }
          }
        }
      } catch (mysqli_sql_exception $e) {
      }?>
    </div>
  </main>

<footer class="text-white pt-5 pb-3" style="background-color: #3e2723">
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

          <!-- Links Úteis (centralizado) -->
          <div
            class="col-md-4 mb-4 d-flex flex-column align-items-center text-center"
          >
            <h5 class="fw-bold">Links Úteis</h5>
            <ul class="list-unstyled small">
              <li>
                <a href="../index.html" class="text-white text-decoration-none"
                  >Início</a
                >
              </li>
              <li>
                <a href="pacientes.php" class="text-white text-decoration-none"
                  >Pacientes</a
                >
              </li>
              <li>
                <a
                  href="profissionais.php"
                  class="text-white text-decoration-none"
                  >Profissionais</a
                >
              </li>
       
            </ul>
          </div>

          <!-- Redes Sociais -->
          <div class="col-md-4 mb-4">
            <h5 class="fw-bold">Redes Sociais</h5>
            <p class="small">Acompanhe nossas novidades e conteúdos:</p>
            <a
              href="https://instagram.com/seuInstagram"
              target="_blank"
              class="text-white me-3"
            >
              <i class="bi bi-instagram fs-4"></i>
            </a>
            <a
              href="https://facebook.com/seuFacebook"
              target="_blank"
              class="text-white me-3"
            >
              <i class="bi bi-facebook fs-4"></i>
            </a>
            <a
              href="https://wa.me/seuNumero"
              target="_blank"
              class="text-white"
            >
              <i class="bi bi-whatsapp fs-4"></i>
            </a>
          </div>
        </div>

        <!-- Linha separadora -->
        <hr class="border-light" />

        <!-- Direitos -->
        <div class="text-center small">
          <p class="mb-0">
            © 2025 Espaço Escuta - Todos os direitos reservados.
          </p>
        </div>
      </div>
    </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
