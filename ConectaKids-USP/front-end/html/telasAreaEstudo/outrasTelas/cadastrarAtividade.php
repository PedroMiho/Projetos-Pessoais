<?php
session_start();
include("../../../../back-end/conexao.php");

// ====== VERIFICA LOGIN ======
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'profissional') {
    header("Location: ../../telaLogin.php");
    exit();
}

$profissional_id = $_SESSION['usuario_id'];

// ====== LISTA DE ALUNOS VINCULADOS ======
$sql = "SELECT p.id, p.nome, p.sobrenome, p.foto_perfil, p.dificuldade
        FROM pacientes p
        INNER JOIN vinculos_profissionais_pacientes v ON v.paciente_id = p.id
        WHERE v.profissional_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $profissional_id);
$stmt->execute();
$alunos = $stmt->get_result();

// ====== CADASTRAR ATIVIDADE COM PRG ======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paciente_id'], $_POST['acao'])) {
    $paciente_id = intval($_POST['paciente_id']);

    if ($_POST['acao'] === 'cadastrar') {
        $nomeAtividade = $_POST['nome_atividade'];
        $descricao = $_POST['descricao'];
        $dataInicio = $_POST['data_inicio'];
        $dataEncerramento = $_POST['data_encerramento'];

        // Upload do PDF
        if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] === 0) {
            $pasta = "uploads/";
            if (!is_dir($pasta)) mkdir($pasta, 0755, true);
            $nomeArquivo = "atividade_" . time() . "_" . basename($_FILES['arquivo_pdf']['name']);
            $caminho = $pasta . $nomeArquivo;
            move_uploaded_file($_FILES['arquivo_pdf']['tmp_name'], $caminho);

            $sqlInsert = "INSERT INTO atividades 
                (profissional_id, paciente_id, nome_atividade, descricao, arquivo_pdf, data_inicio, data_encerramento)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sqlInsert);
            $stmt->bind_param("iisssss", $profissional_id, $paciente_id, $nomeAtividade, $descricao, $caminho, $dataInicio, $dataEncerramento);
            $stmt->execute();
        }
        header("Location: cadastrarAtividade.php?aluno=$paciente_id&msg=sucesso");
        exit();
    }

    // ====== EXCLUIR ATIVIDADE ======
    if ($_POST['acao'] === 'excluir' && isset($_POST['atividade_id'])) {
        $atividade_id = intval($_POST['atividade_id']);
        $sqlDel = "DELETE FROM atividades WHERE id = ? AND profissional_id = ?";
        $stmt = $conn->prepare($sqlDel);
        $stmt->bind_param("ii", $atividade_id, $profissional_id);
        $stmt->execute();
        header("Location: cadastrarAtividade.php?aluno=$paciente_id&msg=excluido");
        exit();
    }
}

// ====== CARREGAR ATIVIDADES ======
$atividades = [];
if (isset($_GET['aluno'])) {
    $aluno_id = intval($_GET['aluno']);
    $sqlAtv = "SELECT * FROM atividades WHERE profissional_id = ? AND paciente_id = ?";
    $stmt = $conn->prepare($sqlAtv);
    $stmt->bind_param("ii", $profissional_id, $aluno_id);
    $stmt->execute();
    $atividades = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastrar Atividade - ConectaKids</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    body { 
        background-color: #f7f3f0; 
        display: flex; 
        flex-direction: column; 
        min-height: 100vh; 
    }
    main { flex: 1; }
    footer { 
        background-color: #3e2723; 
        color: white; 
        padding: 1.5rem 0; 
        text-align: center; 
        margin-top: auto; 
    }
    .card-aluno:hover { transform: scale(1.03); cursor: pointer; }
    .foto-aluno { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 3px solid #6d4c41; }
    .btn-principal { background-color: #6d4c41; color: white; font-weight: 500; }
  </style>
</head>
<body>

<header>
  <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
    <div class="container-fluid">
      <a class="navbar-brand text-white fw-bold fs-5" href="#">ConectaKids</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav w-100">
          <li class="nav-item"><a class="nav-link text-white fs-5" href="../../profissionais.php">Profissionais</a></li>
          <li class="nav-item"><a class="nav-link text-white fs-5" href="../../pacientes.php">Pacientes</a></li>
          <li class="nav-item"><a class="nav-link text-white fs-5" href="../../telasAreaEstudo/areaEstudo.php">Área de Estudos</a></li>
          <li class="nav-item ms-auto">
            <a class="nav-link text-white d-flex align-items-center fs-5" href="../../painel.php">
              <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['usuario_nome']); ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<main class="container py-5">
  <h2 class="text-center mb-4" style="color: #3e2723;">Cadastrar Atividade</h2>

  <?php if (isset($_GET['msg'])): ?>
      <?php if ($_GET['msg'] === 'sucesso'): ?>
        <div class="alert alert-success text-center">Atividade cadastrada com sucesso!</div>
      <?php elseif ($_GET['msg'] === 'excluido'): ?>
        <div class="alert alert-warning text-center">Atividade excluída com sucesso!</div>
      <?php endif; ?>
  <?php endif; ?>

  <!-- Lista de Alunos Vinculados -->
  <div class="row mb-5">
    <h4 class="mb-3 text-center" style="color:#3e2723;">Selecione um aluno</h4>
    <?php while ($aluno = $alunos->fetch_assoc()): 
      $foto = !empty($aluno['foto_perfil']) && file_exists("../../telasProfissional/uploads/" . $aluno['foto_perfil'])
        ? "../../telasProfissional/uploads/" . $aluno['foto_perfil']
        : "../../../imagens/usuario-default.png";
    ?>
    <div class="col-md-3 mb-4">
      <div class="card text-center p-3 card-aluno" onclick="window.location='?aluno=<?= $aluno['id'] ?>'">
        <img src="<?= $foto ?>" class="foto-aluno mx-auto mb-2">
        <h6><?= $aluno['nome'] . ' ' . $aluno['sobrenome'] ?></h6>
        <small class="text-muted"><?= $aluno['dificuldade'] ?></small>
      </div>
    </div>
    <?php endwhile; ?>
  </div>

  <!-- Cadastrar nova atividade -->
  <?php if (isset($_GET['aluno'])): ?>
  <div class="card p-4 mb-5">
    <h5 class="text-center mb-4" style="color: #3e2723;">Cadastrar nova atividade</h5>
    <form method="POST" enctype="multipart/form-data">
      <input type="hidden" name="paciente_id" value="<?= $_GET['aluno'] ?>">
      <input type="hidden" name="acao" value="cadastrar">
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Nome da Atividade</label>
          <input type="text" class="form-control" name="nome_atividade" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">Arquivo PDF</label>
          <input type="file" class="form-control" name="arquivo_pdf" accept="application/pdf" required>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label fw-bold">Descrição</label>
        <textarea class="form-control" name="descricao" rows="3"></textarea>
      </div>
      <div class="row g-3 mb-3">
        <div class="col-md-6">
          <label class="form-label fw-bold">Data de Início</label>
          <input type="date" class="form-control" name="data_inicio" required>
        </div>
        <div class="col-md-6">
          <label class="form-label fw-bold">Data de Encerramento</label>
          <input type="date" class="form-control" name="data_encerramento" required>
        </div>
      </div>
      <div class="text-center">
        <button type="submit" class="btn btn-principal px-5">Cadastrar</button>
      </div>
    </form>
  </div>

  <!-- Tabela de Atividades -->
  <h5 class="text-center mb-3" style="color:#3e2723;">Atividades deste aluno</h5>
  <div class="table-responsive">
    <table class="table table-striped align-middle">
      <thead class="table-dark text-center">
        <tr>
          <th>Nome</th>
          <th>Data Início</th>
          <th>Encerramento</th>
          <th>Arquivo</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody class="text-center">
        <?php if ($atividades && $atividades->num_rows > 0): ?>
          <?php while ($atv = $atividades->fetch_assoc()): ?>
            <tr>
              <td><?= htmlspecialchars($atv['nome_atividade']) ?></td>
              <td><?= $atv['data_inicio'] ?></td>
              <td><?= $atv['data_encerramento'] ?></td>
              <td><a href="<?= $atv['arquivo_pdf'] ?>" target="_blank" class="btn btn-outline-dark btn-sm"><i class="bi bi-file-earmark-pdf"></i> Abrir</a></td>
              <td><?= (strtotime($atv['data_encerramento']) < time()) ? '<span class="text-danger">Encerrada</span>' : '<span class="text-success">Em andamento</span>' ?></td>
              <td>
                <form method="POST" style="display:inline-block;">
                  <input type="hidden" name="paciente_id" value="<?= $_GET['aluno'] ?>">
                  <input type="hidden" name="atividade_id" value="<?= $atv['id'] ?>">
                  <input type="hidden" name="acao" value="excluir">
                  <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i> Excluir</button>
                </form>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="6" class="text-muted">Nenhuma atividade cadastrada.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>

</main>

<footer>
  <div class="container small">
    <p class="mb-0">© 2025 ConectaKids - Todos os direitos reservados.</p>
  </div>
</footer>

</body>
</html>
