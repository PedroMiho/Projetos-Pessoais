<?php
session_start();
include("../../back-end/conexao.php");

// Consulta pacientes
try {
    $sql = "SELECT nome, email, telefone, especialidade, foto_perfil, descricao FROM profissionais";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $resultado = $stmt->get_result();
} catch (mysqli_sql_exception $e) {
    $resultado = false;
}

// Verifica login
$usuario_logado = isset($_SESSION['usuario_id']);
$usuario_nome = $usuario_logado ? $_SESSION['usuario_nome'] : null;

// Define link da área de estudos conforme tipo de usuário
if ($usuario_logado) {
    if ($_SESSION['usuario_tipo'] === 'profissional') {
        $linkEstudos = "telasAreaEstudo/areaEstudosProfissional.php";
    } else {
        $linkEstudos = "telasAreaEstudoAluno/areaEstudoAluno.php";
    }
} else {
    $linkEstudos = "telaLogin.php";
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profissionais - ConectaKids</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
  <style>
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
    }

    main { flex: 1; }

    body { background-color: #ede7e3; }

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
        <a class="navbar-brand text-white fw-bold fs-5" href="../index.php">ConectaKids</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav w-100">
            <li class="nav-item"><a class="nav-link text-white active fs-5" href="#">Profissionais</a></li>
            <li class="nav-item"><a class="nav-link text-white fs-5" href="pacientes.php">Pacientes</a></li>
            <li class="nav-item"><a class="nav-link text-white fs-5" href="<?php echo $linkEstudos; ?>">Área de Estudos</a></li>

            <!-- Login ou nome do usuário -->
            <li class="nav-item ms-auto">
              <?php if ($usuario_logado): ?>
                <a class="nav-link text-white d-flex align-items-center fs-5" href="telasAreaEstudo/areaEstudo.php">
                  <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($usuario_nome); ?>
                </a>
              <?php else: ?>
                <a class="nav-link text-white d-flex align-items-center fs-5" href="telaLogin.php">
                  <i class="bi bi-person-circle me-1"></i> Login
                </a>
              <?php endif; ?>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- Main -->
  <main>
    <div class="container py-5">
      <h2 class="text-center mb-4">Profissionais</h2>

      <?php
      if ($resultado && $resultado->num_rows > 0) {
        while ($row = $resultado->fetch_assoc()) {
          $telefone = $row['telefone'] ?? '';
          $telefoneFormatado = preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);

          // Exibe imagem de perfil se existir
          $foto = !empty($row['foto_perfil']) && file_exists("telasProfissional/uploads/" . $row['foto_perfil'])
            ? "telasProfissional/uploads/" . $row['foto_perfil']
            : "../imagens/usuario-default.png";

          echo "
            <div class='card-custom'>
              <div class='card-left'>
                <img src='$foto' alt='Foto de {$row['nome']}'>
                <h5>{$row['nome']}</h5>
                <p class='text-muted'>{$row['especialidade']}</p>
              </div>
              <div class='card-right'>
                <p class='info-item'><strong>Telefone:</strong> {$telefoneFormatado}</p>
                <p class='info-item'><strong>E-mail:</strong> {$row['email']}</p>
                <p class='info-item'><strong>Descrição:</strong> {$row['descricao']}</p>
              </div>
            </div>
          ";
        }
      } else {
        echo "<p class='text-center text-muted'>Nenhum profissional cadastrado ainda.</p>";
      }
      ?>
    </div>
  </main>

  <!-- Footer -->
  <footer class="text-white pt-5 pb-3" style="background-color: #3e2723">
    <div class="container text-center small">
      <p class="mb-0">© 2025 ConectaKids - Todos os direitos reservados.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
