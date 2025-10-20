<?php
session_start();
include("../../../../back-end/conexao.php");

// ====== VERIFICA LOGIN ======
function estaLogado()
{
    return isset($_SESSION['usuario_id']);
}

if (!estaLogado() || $_SESSION['usuario_tipo'] !== 'profissional') {
    header("Location: ../../telaLogin.php");
    exit();
}

$profissional_id = $_SESSION['usuario_id'];
$mensagem = "";

// ===== CADASTRO DE NOVO VÍNCULO =====
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email_aluno'])) {
    $emailAluno = trim($_POST['email_aluno']);

    // Verifica se o aluno existe
    $sqlAluno = "SELECT id FROM pacientes WHERE email = ?";
    $stmt = $conn->prepare($sqlAluno);
    $stmt->bind_param("s", $emailAluno);
    $stmt->execute();
    $resAluno = $stmt->get_result();

    if ($resAluno->num_rows > 0) {
        $aluno = $resAluno->fetch_assoc();
        $aluno_id = $aluno['id'];

        // Verifica se já há vínculo
        $sqlVerifica = "SELECT * FROM vinculos_profissionais_pacientes WHERE profissional_id = ? AND paciente_id = ?";
        $stmtVerifica = $conn->prepare($sqlVerifica);
        $stmtVerifica->bind_param("ii", $profissional_id, $aluno_id);
        $stmtVerifica->execute();
        $resVerifica = $stmtVerifica->get_result();

        if ($resVerifica->num_rows > 0) {
            $mensagem = "<div class='alert alert-warning text-center'>Este aluno já está vinculado a você.</div>";
        } else {
            // Cadastra novo vínculo
            $sqlInsert = "INSERT INTO vinculos_profissionais_pacientes (profissional_id, paciente_id) VALUES (?, ?)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("ii", $profissional_id, $aluno_id);
            if ($stmtInsert->execute()) {
                $mensagem = "<div class='alert alert-success text-center'>Aluno vinculado com sucesso!</div>";
            } else {
                $mensagem = "<div class='alert alert-danger text-center'>Erro ao vincular aluno.</div>";
            }
        }
    } else {
        $mensagem = "<div class='alert alert-danger text-center'>Nenhum aluno encontrado com este e-mail.</div>";
    }
}

// ===== LISTA DE ALUNOS VINCULADOS =====
$sqlLista = "
  SELECT p.id, p.nome, p.sobrenome, p.email, p.telefone, p.dificuldade, p.foto_perfil
  FROM pacientes p
  INNER JOIN vinculos_profissionais_pacientes v ON v.paciente_id = p.id
  WHERE v.profissional_id = ?
";
$stmt = $conn->prepare($sqlLista);
$stmt->bind_param("i", $profissional_id);
$stmt->execute();
$resLista = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Aluno - ConectaKids</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    html, body {
      height: 100%;
      margin: 0;
      display: flex;
      flex-direction: column;
    }
    main { flex: 1; }
    footer {
      margin-top: auto;
      background-color: #3e2723;
      color: white;
      text-align: center;
      padding: 2rem 0;
      width: 100%;
    }
    body { background-color: #f7f3f0; }
    .card {
      border-radius: 15px;
      background-color: #fff;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: transform 0.2s;
      position: relative;
    }
    .card:hover { transform: scale(1.02); }
    .foto-aluno {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 50%;
      border: 3px solid #6d4c41;
    }
    .btn-principal {
      background-color: #6d4c41;
      color: white;
      font-weight: 500;
    }
    .btn-remover {
      position: absolute;
      top: 10px;
      right: 10px;
      background: none;
      border: none;
      color: #a94442;
      cursor: pointer;
    }
    .btn-remover:hover {
      color: #7b2f2f;
      transform: scale(1.1);
    }
  </style>
</head>

<body>

  <!-- Header -->
  <header>
    <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
      <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold fs-5" href="#">ConectaKids</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav w-100">
            <li class="nav-item"><a class="nav-link text-white fs-5" href="../../profissionais.php">Profissionais</a></li>
            <li class="nav-item"><a class="nav-link text-white fs-5" href="../../pacientes.php">Pacientes</a></li>
            <li class="nav-item"><a class="nav-link text-white fs-5" href="../../telasAreaEstudo/areaEstudo.php">Área de Estudos</a></li>
            <li class="nav-item ms-auto">
              <?php if (estaLogado()): ?>
                <a class="nav-link text-white d-flex align-items-center fs-5" href="../areaEstudo.php">
                  <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['usuario_nome']); ?>
                </a>
              <?php else: ?>
                <a class="nav-link text-white d-flex align-items-center fs-5" href="../../telaLogin.php">
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
  <div class="container py-5">
    <h2 class="text-center mb-4" style="color: #3e2723;">Cadastrar Aluno</h2>
    <?= $mensagem ?>

    <!-- Formulário de cadastro -->
    <div class="card p-4 mb-5">
      <h5 class="mb-3 text-center" style="color: #3e2723;">Vincular aluno existente</h5>
      <form method="POST">
        <div class="row g-3 align-items-center justify-content-center">
          <div class="col-md-6">
            <input type="email" class="form-control" name="email_aluno" placeholder="E-mail do aluno cadastrado" required>
          </div>
          <div class="col-md-2 text-center">
            <button type="submit" class="btn btn-principal w-100">
              <i class="bi bi-person-plus me-1"></i> Vincular
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Lista de alunos vinculados -->
    <h4 class="mb-4 text-center" style="color: #3e2723;">Seus Alunos Vinculados</h4>
    <div class="row">
      <?php if ($resLista->num_rows > 0): ?>
        <?php while ($aluno = $resLista->fetch_assoc()): ?>
          <?php
          $foto = !empty($aluno['foto_perfil']) && file_exists("../../telasProfissional/uploads/" . $aluno['foto_perfil'])
            ? "../../telasProfissional/uploads/" . $aluno['foto_perfil']
            : "../../../imagens/usuario-default.png";
          ?>
          <div class='col-md-4 mb-4'>
            <div class='card text-center p-3'>
              <!-- Botão de remover -->
              <form method="POST" action="removerAluno.php" onsubmit="return confirm('Deseja remover este aluno?');">
                <input type="hidden" name="paciente_id" value="<?= $aluno['id'] ?>">
                <button type="submit" class="btn-remover" title="Remover Aluno">
                  <i class="bi bi-trash3-fill fs-5"></i>
                </button>
              </form>

              <img src='<?= $foto ?>' class='foto-aluno mx-auto mb-3'>
              <h5><?= htmlspecialchars($aluno['nome'] . " " . $aluno['sobrenome']) ?></h5>
              <p class='text-muted mb-1'><?= htmlspecialchars($aluno['dificuldade']) ?></p>
              <p class='small mb-0'><strong>E-mail:</strong> <?= htmlspecialchars($aluno['email']) ?></p>
              <p class='small'><strong>Telefone:</strong> <?= htmlspecialchars($aluno['telefone']) ?></p>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p class="text-center text-muted">Nenhum aluno vinculado até o momento.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Footer -->
  <footer class="text-white pt-5 pb-3">
    <div class="container text-center small">
      <p class="mb-0">© 2025 Espaço Escuta - Todos os direitos reservados.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
