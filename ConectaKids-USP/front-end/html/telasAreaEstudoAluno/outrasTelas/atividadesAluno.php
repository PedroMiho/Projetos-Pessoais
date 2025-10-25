<?php
session_start();
include("../../../../back-end/conexao.php");
// Função para verificar login via sessão ou cookie
function estaLogado()
{
  global $conn; // permite usar $conn dentro da função

  if (isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_tipo'])) {
    return true;
  } elseif (isset($_COOKIE['usuario_id'])) {
    $usuario_id = $_COOKIE['usuario_id'];
    $sql = "SELECT id, nome, tipo FROM usuarios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
      $row = $result->fetch_assoc();
      $_SESSION['usuario_id'] = $row['id'];
      $_SESSION['usuario_nome'] = $row['nome'];
      $_SESSION['usuario_tipo'] = $row['tipo']; // salva tipo (profissional ou aluno)
      return true;
    }
  }
  return false;
}

// Decide o link da Área de Estudos conforme tipo de usuário
if (estaLogado()) {
  if ($_SESSION['usuario_tipo'] === 'profissional') {
    $linkEstudos = "html/telasAreaEstudo/areaEstudo.php";
  } else { // aluno
    $linkEstudos = "../areaEstudoAluno.php";
  }
} else {
  $linkEstudos = "html/telaLogin.php";
}

// ====== VERIFICA LOGIN ======
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'paciente') {
  header("Location: ../../telaLogin.php");
  exit();
}

$aluno_id = $_SESSION['usuario_id'];

// ====== BUSCA TODAS AS ATIVIDADES VINCULADAS ======
$sql = "SELECT a.id AS atividade_id, a.nome_atividade, a.descricao, a.arquivo_pdf, a.data_inicio, a.data_encerramento,
               e.arquivo_entregue, e.data_entrega
        FROM atividades a
        LEFT JOIN entregas_atividades e ON e.atividade_id = a.id AND e.paciente_id = ?
        WHERE a.paciente_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $aluno_id, $aluno_id);
$stmt->execute();
$atividades = $stmt->get_result();

// ====== ENTREGA DE ATIVIDADE ======
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['atividade_id'])) {
  $atividade_id = intval($_POST['atividade_id']);

  if (isset($_FILES['arquivo_entregue']) && $_FILES['arquivo_entregue']['error'] === 0) {
    $pasta = "entregas/";
    if (!is_dir($pasta)) mkdir($pasta, 0755, true);
    $nomeArquivo = "entrega_" . time() . "_" . basename($_FILES['arquivo_entregue']['name']);
    $caminho = $pasta . $nomeArquivo;
    move_uploaded_file($_FILES['arquivo_entregue']['tmp_name'], $caminho);

    // Verifica se já existe entrega
    $check = $conn->prepare("SELECT id FROM entregas_atividades WHERE atividade_id = ? AND paciente_id = ?");
    $check->bind_param("ii", $atividade_id, $aluno_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
      $sqlUp = "UPDATE entregas_atividades SET arquivo_entregue = ?, data_entrega = NOW() 
                WHERE atividade_id = ? AND paciente_id = ?";
      $stmt = $conn->prepare($sqlUp);
      $stmt->bind_param("sii", $caminho, $atividade_id, $aluno_id);
    } else {
      $sqlIns = "INSERT INTO entregas_atividades (atividade_id, paciente_id, arquivo_entregue, data_entrega) 
                 VALUES (?, ?, ?, NOW())";
      $stmt = $conn->prepare($sqlIns);
      $stmt->bind_param("iis", $atividade_id, $aluno_id, $caminho);
    }
    $stmt->execute();

    header("Location: atividadesAluno.php");
    exit();
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Minhas Atividades - ConectaKids</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    body { background-color: #f7f3f0; display: flex; flex-direction: column; min-height: 100vh; }
    main { flex: 1; }
    footer { background-color: #3e2723; color: white; padding: 1.5rem 0; text-align: center; margin-top: auto; }
    .card-atividade { border-left: 6px solid #6d4c41; border-radius: 10px; padding: 15px; margin-bottom: 15px; background: #fff; }
    .btn-principal { background-color: #6d4c41; color: white; font-weight: 500; }
    .btn-principal:hover { background-color: #5d4037; color: white; }
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
            <!-- Links à esquerda -->
            <li class="nav-item">
              <a class="nav-link text-white active fs-5" aria-current="page" href="../../profissionais.php">Profissionais</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="../../pacientes.php">Pacientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="<?php echo $linkEstudos; ?>">Área de Estudos</a>
            </li>
            <!-- Login à direita -->
            <li class="nav-item ms-auto">
              <?php if (estaLogado()): ?>
                <?php
                // Define o link correto conforme o tipo de usuário
                if ($_SESSION['usuario_tipo'] === 'profissional') {
                  $linkEstudos = "../../telasAreaEstudoAluno/areaEstudoAluno.php";
                } else {
                  $linkUsuario = $linkEstudos = "../../telasAreaEstudoAluno/areaEstudoAluno.php";
                }
                ?>
                <a class="nav-link text-white d-flex align-items-center fs-5" href="<?php echo $linkUsuario; ?>">
                  <i class="bi bi-person-circle me-1"></i> <?php echo htmlspecialchars($_SESSION['usuario_nome']); ?>
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

<!-- Main -->
<main class="container py-5">
  <h2 class="text-center mb-4" style="color: #3e2723;">Minhas Atividades</h2>

  <?php if (isset($_GET['msg']) && $_GET['msg'] === 'entregue'): ?>
    <div class="alert alert-success text-center">Atividade entregue com sucesso!</div>
  <?php endif; ?>

  <?php if ($atividades->num_rows === 0): ?>
    <p class="text-center text-muted">Nenhuma atividade disponível no momento.</p>
  <?php else: ?>
    <?php while ($a = $atividades->fetch_assoc()): 
      $dataInicio = date("d/m/Y", strtotime($a['data_inicio']));
      $dataEncerramento = date("d/m/Y", strtotime($a['data_encerramento']));
      $hoje = date("Y-m-d");

      // Define o status da atividade
      if (!empty($a['arquivo_entregue'])) {
        $status = "<span class='badge bg-success'>Entregue</span>";
      } elseif ($a['data_encerramento'] < $hoje) {
        $status = "<span class='badge bg-danger'>Encerrada</span>";
      } else {
        $status = "<span class='badge bg-warning text-dark'>Pendente</span>";
      }
    ?>
      <div class="card-atividade shadow-sm">
        <div class="d-flex justify-content-between align-items-center">
          <h5 class="mb-1"><?= htmlspecialchars($a['nome_atividade']) ?></h5>
          <?= $status ?>
        </div>
        <p class="text-muted mb-2 small"><?= htmlspecialchars($a['descricao']) ?></p>
        <p class="mb-1"><strong>Início:</strong> <?= $dataInicio ?> | <strong>Encerramento:</strong> <?= $dataEncerramento ?></p>

        <a href="../../telasAreaEstudo/outrasTelas/<?= $a['arquivo_pdf'] ?>" target="_blank" class="btn btn-outline-dark btn-sm mt-2">
          <i class="bi bi-file-earmark-pdf"></i> Ver Atividade
        </a>

        <?php if (!empty($a['arquivo_entregue'])): ?>
          <a href="../outrasTelas/<?= $a['arquivo_entregue'] ?>" target="_blank" class="btn btn-outline-success btn-sm mt-2">
            <i class="bi bi-check-circle"></i> Ver Entrega
          </a>

          <?php if ($a['data_encerramento'] >= $hoje): ?>
            <!-- Permitir reenviar caso o prazo ainda esteja válido -->
            <form method="POST" enctype="multipart/form-data" class="mt-2">
              <input type="hidden" name="atividade_id" value="<?= $a['atividade_id'] ?>">
              <div class="input-group">
                <input type="file" name="arquivo_entregue" class="form-control" accept="application/pdf" required>
                <button type="submit" class="btn btn-warning text-dark">
                  <i class="bi bi-arrow-repeat"></i> Alterar Entrega
                </button>
              </div>
            </form>
          <?php endif; ?>

        <?php elseif ($a['data_encerramento'] >= $hoje): ?>
          <!-- Primeira entrega -->
          <form method="POST" enctype="multipart/form-data" class="mt-3">
            <input type="hidden" name="atividade_id" value="<?= $a['atividade_id'] ?>">
            <div class="input-group">
              <input type="file" name="arquivo_entregue" class="form-control" accept="application/pdf" required>
              <button type="submit" class="btn btn-principal">
                <i class="bi bi-upload"></i> Enviar
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    <?php endwhile; ?>
  <?php endif; ?>
</main>

<!-- Footer -->
<footer>
  <div class="container small">
    <p class="mb-0">© 2025 ConectaKids - Todos os direitos reservados.</p>
  </div>
</footer>

</body>
</html>
