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

// ====== AÇÕES ======
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao'])) {
    $paciente_id = intval($_POST['paciente_id']);

    // Cadastrar nova atividade
    if ($_POST['acao'] === 'cadastrar') {
        $nomeAtividade = $_POST['nome_atividade'];
        $descricao = $_POST['descricao'];
        $dataInicio = $_POST['data_inicio'];
        $dataEncerramento = $_POST['data_encerramento'];

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

    // Excluir atividade
    if ($_POST['acao'] === 'excluir' && isset($_POST['atividade_id'])) {
        $atividade_id = intval($_POST['atividade_id']);

        // Apaga entregas relacionadas
        $conn->prepare("DELETE FROM entregas_atividades WHERE atividade_id = ?")->execute([$atividade_id]);

        // Apaga a própria atividade
        $sqlDel = "DELETE FROM atividades WHERE id = ? AND profissional_id = ?";
        $stmt = $conn->prepare($sqlDel);
        $stmt->bind_param("ii", $atividade_id, $profissional_id);
        $stmt->execute();

        header("Location: cadastrarAtividade.php?aluno=$paciente_id&msg=excluido");
        exit();
    }

    // Editar data e/ou arquivo PDF
    if ($_POST['acao'] === 'editar' && isset($_POST['atividade_id'])) {
        $atividade_id = intval($_POST['atividade_id']);
        $novaData = $_POST['nova_data'];

        // Atualiza a data de encerramento
        $sqlUp = "UPDATE atividades SET data_encerramento = ? WHERE id = ? AND profissional_id = ?";
        $stmt = $conn->prepare($sqlUp);
        $stmt->bind_param("sii", $novaData, $atividade_id, $profissional_id);
        $stmt->execute();

        // Atualiza o PDF se enviado e ainda em andamento
        if (isset($_FILES['arquivo_pdf']) && $_FILES['arquivo_pdf']['error'] === 0) {
            $sqlCheck = "SELECT data_encerramento FROM atividades WHERE id = ? AND profissional_id = ?";
            $stmt = $conn->prepare($sqlCheck);
            $stmt->bind_param("ii", $atividade_id, $profissional_id);
            $stmt->execute();
            $res = $stmt->get_result()->fetch_assoc();

            if (strtotime($res['data_encerramento']) >= time()) {
                $pasta = "uploads/";
                if (!is_dir($pasta)) mkdir($pasta, 0755, true);
                $nomeArquivo = "atividade_" . time() . "_" . basename($_FILES['arquivo_pdf']['name']);
                $caminho = $pasta . $nomeArquivo;
                move_uploaded_file($_FILES['arquivo_pdf']['tmp_name'], $caminho);

                $sqlPdf = "UPDATE atividades SET arquivo_pdf = ? WHERE id = ? AND profissional_id = ?";
                $stmt = $conn->prepare($sqlPdf);
                $stmt->bind_param("sii", $caminho, $atividade_id, $profissional_id);
                $stmt->execute();
            }
        }

        header("Location: cadastrarAtividade.php?aluno=$paciente_id&msg=editado");
        exit();
    }
}

// ====== CARREGAR ATIVIDADES DO ALUNO ======
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
body { background-color: #f7f3f0; display: flex; flex-direction: column; min-height: 100vh; }
main { flex: 1; }
footer { background-color: #3e2723; color: white; padding: 1.5rem 0; text-align: center; margin-top: auto; }
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
  <?php
  $mensagens = [
    'sucesso' => ['success', 'Atividade cadastrada com sucesso!'],
    'excluido' => ['warning', 'Atividade excluída com sucesso!'],
    'editado' => ['info', 'Atividade atualizada com sucesso!']
  ];
  if (isset($mensagens[$_GET['msg']])) {
    [$tipo, $texto] = $mensagens[$_GET['msg']];
    echo "<div class='alert alert-$tipo text-center'>$texto</div>";
  }
  ?>
<?php endif; ?>

<!-- Lista de alunos -->
<div class="row mb-5">
<h4 class="mb-3 text-center" style="color:#3e2723;">Selecione um aluno</h4>
<?php while ($aluno = $alunos->fetch_assoc()): 
  $foto = !empty($aluno['foto_perfil']) && file_exists("../../telasProfissional/uploads/" . $aluno['foto_perfil'])
    ? "../../telasProfissional/uploads/" . $aluno['foto_perfil']
    : "../../../imagens/usuario-default.png";

  $ativo = (isset($_GET['aluno']) && $_GET['aluno'] == $aluno['id']);
  $link = $ativo ? "cadastrarAtividade.php" : "cadastrarAtividade.php?aluno=" . $aluno['id'];
?>
<div class="col-md-3 mb-4">
  <a href="<?= $link ?>" class="text-decoration-none text-dark">
    <div class="card text-center p-3 card-aluno <?= $ativo ? 'border border-3 border-success' : '' ?>">
      <img src="<?= $foto ?>" class="foto-aluno mx-auto mb-2">
      <h6><?= htmlspecialchars($aluno['nome'] . ' ' . $aluno['sobrenome']) ?></h6>
      <small class="text-muted"><?= htmlspecialchars($aluno['dificuldade']) ?></small>
    </div>
  </a>
</div>
<?php endwhile; ?>
</div>

<!-- Exibir formulário e tabela -->
<?php if (isset($_GET['aluno'])): ?>
  <div id="areaAtividade">
    <!-- Formulário -->
    <div class="card p-4 mb-4">
      <h5 class="text-center mb-3" style="color: #3e2723;">Cadastrar nova atividade</h5>
      <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="paciente_id" value="<?= intval($_GET['aluno']) ?>">
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

    <!-- Tabela -->
    <div class="card p-3">
      <h5 class="text-center mb-3" style="color:#3e2723;">Atividades cadastradas</h5>
      <div class="table-responsive">
        <table class="table table-striped align-middle text-center">
          <thead class="table-dark">
            <tr>
              <th>Nome</th>
              <th>Início</th>
              <th>Encerramento</th>
              <th>Arquivo</th>
              <th>Status</th>
              <th>Ações</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($atividades && $atividades->num_rows > 0): ?>
              <?php while ($atv = $atividades->fetch_assoc()): ?>
                <tr>
                  <td><?= htmlspecialchars($atv['nome_atividade']) ?></td>
                  <td><?= $atv['data_inicio'] ?></td>
                  <td><?= $atv['data_encerramento'] ?></td>
                  <td><a href="<?= $atv['arquivo_pdf'] ?>" target="_blank" class="btn btn-outline-dark btn-sm"><i class="bi bi-file-earmark-pdf"></i> Abrir</a></td>
                  <td><?= (strtotime($atv['data_encerramento']) < strtotime(date('Y-m-d')))
                        ? '<span class="text-danger">Encerrada</span>'
                        : '<span class="text-success">Em andamento</span>' ?></td>
                  <td>
                    <!-- Excluir -->
                    <form method="POST" class="d-inline">
                      <input type="hidden" name="paciente_id" value="<?= $_GET['aluno'] ?>">
                      <input type="hidden" name="atividade_id" value="<?= $atv['id'] ?>">
                      <input type="hidden" name="acao" value="excluir">
                      <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                    </form>

                    <!-- Editar -->
                    <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editar<?= $atv['id'] ?>">
                      <i class="bi bi-pencil"></i>
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="editar<?= $atv['id'] ?>" tabindex="-1">
                      <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                          <form method="POST" enctype="multipart/form-data">
                            <div class="modal-header">
                              <h5 class="modal-title">Editar Atividade</h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                              <input type="hidden" name="paciente_id" value="<?= $_GET['aluno'] ?>">
                              <input type="hidden" name="atividade_id" value="<?= $atv['id'] ?>">
                              <input type="hidden" name="acao" value="editar">
                              <label class="form-label">Nova Data de Encerramento</label>
                              <input type="date" name="nova_data" class="form-control mb-3" value="<?= $atv['data_encerramento'] ?>" required>
                              <?php if (strtotime($atv['data_encerramento']) >= time()): ?>
                                <label class="form-label">Substituir Arquivo PDF</label>
                                <input type="file" class="form-control" name="arquivo_pdf" accept="application/pdf">
                              <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                              <button type="submit" class="btn btn-principal">Salvar</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6" class="text-muted">Nenhuma atividade cadastrada.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<?php endif; ?>

</main>

<footer>
<div class="container small">
  <p class="mb-0">© 2025 ConectaKids - Todos os direitos reservados.</p>
</div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
