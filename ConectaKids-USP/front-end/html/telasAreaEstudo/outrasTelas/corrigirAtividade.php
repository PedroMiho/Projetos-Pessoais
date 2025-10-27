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

// ====== ATIVIDADES DO ALUNO SELECIONADO ======
$atividades = [];
if (isset($_GET['aluno'])) {
  $aluno_id = $_GET['aluno'];
  $sqlAtividades = "
    SELECT a.id, a.nome_atividade, a.descricao, a.arquivo_pdf, a.data_inicio, a.data_encerramento, 
           e.id AS entrega_id, e.arquivo_entregue, e.data_entrega, e.nota
    FROM atividades a
    LEFT JOIN entregas_atividades e ON e.atividade_id = a.id AND e.paciente_id = ?
    WHERE a.profissional_id = ? AND a.paciente_id = ?
  ";
  $stmtAtividades = $conn->prepare($sqlAtividades);
  $stmtAtividades->bind_param("iii", $aluno_id, $profissional_id, $aluno_id);
  $stmtAtividades->execute();
  $atividades = $stmtAtividades->get_result();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acompanhamento de Atividades - ConectaKids</title>
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
      margin-top: auto;
      text-align: center;
    }
    .foto-aluno {
      width: 100px; height: 100px; object-fit: cover;
      border-radius: 50%; border: 3px solid #6d4c41;
    }
    .card-aluno:hover { transform: scale(1.05); cursor: pointer; }
    .section-title {
      background-color: #6d4c41; color: white;
      padding: 10px; border-radius: 10px;
    }
    .card-atividade {
      border-radius: 10px; border-left: 6px solid #6d4c41;
      margin-bottom: 10px; background-color: #fff;
    }
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
  <h2 class="text-center mb-4" style="color: #3e2723;">Acompanhamento de Atividades</h2>

  <?php if (!isset($_GET['aluno'])): ?>
    <div class="row justify-content-center">
      <h5 class="text-center mb-4" style="color: #3e2723;">Selecione um aluno para visualizar as atividades</h5>
      <?php while ($aluno = $alunos->fetch_assoc()):
        $foto = !empty($aluno['foto_perfil']) && file_exists("../../telasProfissional/uploads/".$aluno['foto_perfil'])
          ? "../../telasProfissional/uploads/".$aluno['foto_perfil']
          : "../../../imagens/usuario-default.png";
      ?>
        <div class="col-md-3 mb-4">
          <a href="?aluno=<?= $aluno['id'] ?>" class="text-decoration-none text-dark">
            <div class="card text-center p-3 card-aluno">
              <img src="<?= $foto ?>" class="foto-aluno mx-auto mb-3">
              <h5><?= $aluno['nome'] ?> <?= $aluno['sobrenome'] ?></h5>
              <p class="text-muted small"><?= $aluno['dificuldade'] ?></p>
            </div>
          </a>
        </div>
      <?php endwhile; ?>
    </div>

  <?php else: ?>
    <?php
    $sqlAluno = "SELECT nome, sobrenome, foto_perfil FROM pacientes WHERE id = ?";
    $stmtA = $conn->prepare($sqlAluno);
    $stmtA->bind_param("i", $aluno_id);
    $stmtA->execute();
    $alunoSel = $stmtA->get_result()->fetch_assoc();
    $foto = !empty($alunoSel['foto_perfil']) && file_exists("../../telasProfissional/uploads/".$alunoSel['foto_perfil'])
      ? "../../telasProfissional/uploads/".$alunoSel['foto_perfil']
      : "../../../imagens/usuario-default.png";
    ?>

    <div class="text-center mb-5">
      <img src="<?= $foto ?>" class="foto-aluno mb-2">
      <h4><?= $alunoSel['nome']." ".$alunoSel['sobrenome'] ?></h4>
      <a href="corrigirAtividade.php" class="btn btn-outline-secondary mt-2">Voltar</a>
    </div>

    <?php
    $entregues = [];
    $andamento = [];
    $vencidas = [];
    $dataAtual = date('Y-m-d');

    while ($atv = $atividades->fetch_assoc()) {
      if (!empty($atv['arquivo_entregue'])) $entregues[] = $atv;
      elseif ($atv['data_encerramento'] < $dataAtual) $vencidas[] = $atv;
      else $andamento[] = $atv;
    }

    function renderAtividade($atv, $entregue = false) {
      $dataInicio = date('d/m/Y', strtotime($atv['data_inicio']));
      $dataFim = date('d/m/Y', strtotime($atv['data_encerramento']));
      $statusEncerrada = strtotime($atv['data_encerramento']) < time();

      echo "<div class='card card-atividade p-3 mb-3'>
        <div class='d-flex justify-content-between align-items-start'>
          <div>
            <h5 class='mb-1'>".htmlspecialchars($atv['nome_atividade'])."</h5>
            <p class='text-muted small mb-2'>".nl2br(htmlspecialchars($atv['descricao']))."</p>
            <p class='mb-2'><strong>Início:</strong> $dataInicio | <strong>Encerramento:</strong> $dataFim</p>";

      if (!empty($atv['arquivo_pdf'])) {
        echo "<a href='{$atv['arquivo_pdf']}' target='_blank' class='btn btn-outline-dark btn-sm mb-2'>
                <i class='bi bi-file-earmark-pdf'></i> PDF da Atividade
              </a><br>";
      }

      if ($entregue) {
        echo "<div class='d-flex align-items-center gap-3 mt-2'>
          <a href='../../telasAreaEstudoAluno/outrasTelas/{$atv['arquivo_entregue']}' target='_blank' class='btn btn-outline-success btn-sm'>
            <i class='bi bi-file-earmark-check'></i> Ver Entrega
          </a>
          <form method='POST' action='salvarNota.php' class='d-flex align-items-center gap-2'>
            <input type='hidden' name='entrega_id' value='{$atv['entrega_id']}'>
            <input type='number' name='nota' class='form-control form-control-sm' style='width: 90px; text-align:center;' 
                   min='0' max='10' step='0.1' value='".htmlspecialchars($atv['nota'] ?? '')."' placeholder='Nota'>
            <button type='submit' class='btn btn-sm btn-primary'>Salvar</button>
          </form>
        </div>";
      }

      echo "</div>";

      echo "<div class='text-end'>
        <button class='btn btn-sm btn-outline-primary me-2' data-bs-toggle='modal' data-bs-target='#editar{$atv['id']}'>
          <i class='bi bi-pencil'></i>
        </button>
        <form method='POST' action='removerAtividade.php' class='d-inline'>
          <input type='hidden' name='atividade_id' value='{$atv['id']}'>
          <input type='hidden' name='paciente_id' value='{$_GET['aluno']}'>
          <button type='submit' class='btn btn-sm btn-outline-danger' onclick='return confirm(\"Tem certeza que deseja remover esta atividade?\")'>
            <i class='bi bi-trash'></i>
          </button>
        </form>
      </div>";

      echo "</div>";

      // Modal de edição
      echo "<div class='modal fade' id='editar{$atv['id']}' tabindex='-1'>
        <div class='modal-dialog modal-dialog-centered'>
          <div class='modal-content'>
            <form method='POST' enctype='multipart/form-data' action='editarAtividade.php'>
              <div class='modal-header'>
                <h5 class='modal-title'>Editar Atividade</h5>
                <button type='button' class='btn-close' data-bs-dismiss='modal'></button>
              </div>
              <div class='modal-body'>
                <input type='hidden' name='atividade_id' value='{$atv['id']}'>
                <input type='hidden' name='paciente_id' value='{$_GET['aluno']}'>
                
                <div class='mb-3'>
                  <label class='form-label'>Nome da Atividade</label>
                  <input type='text' name='nome_atividade' class='form-control' value='".htmlspecialchars($atv['nome_atividade'])."' required>
                </div>
                <div class='mb-3'>
                  <label class='form-label'>Descrição</label>
                  <textarea name='descricao' class='form-control' rows='3'>".htmlspecialchars($atv['descricao'])."</textarea>
                </div>
                <div class='mb-3'>
                  <label class='form-label'>Nova Data de Encerramento</label>
                  <input type='date' name='data_encerramento' class='form-control' value='".htmlspecialchars($atv['data_encerramento'])."' required>
                </div>";

      if (!$statusEncerrada) {
        echo "<div class='mb-3'>
          <label class='form-label'>Substituir PDF (opcional)</label>
          <input type='file' name='arquivo_pdf' class='form-control' accept='application/pdf'>
        </div>";
      }

      echo "</div>
            <div class='modal-footer'>
              <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Cancelar</button>
              <button type='submit' class='btn btn-primary'>Salvar Alterações</button>
            </div>
          </form>
        </div>
      </div>
    </div>";
    }
    ?>

    <div class="mb-4">
      <h5 class="section-title"><i class="bi bi-check-circle me-2"></i>Entregues</h5>
      <?= empty($entregues) ? "<p class='text-muted ms-2 mt-2'>Nenhuma atividade entregue.</p>" : "" ?>
      <?php foreach ($entregues as $a) renderAtividade($a, true); ?>
    </div>

    <div class="mb-4">
      <h5 class="section-title"><i class="bi bi-hourglass-split me-2"></i>Em andamento</h5>
      <?= empty($andamento) ? "<p class='text-muted ms-2 mt-2'>Nenhuma atividade em andamento.</p>" : "" ?>
      <?php foreach ($andamento as $a) renderAtividade($a); ?>
    </div>

    <div class="mb-4">
      <h5 class="section-title"><i class="bi bi-exclamation-triangle me-2"></i>Vencidas</h5>
      <?= empty($vencidas) ? "<p class='text-muted ms-2 mt-2'>Nenhuma atividade vencida.</p>" : "" ?>
      <?php foreach ($vencidas as $a) renderAtividade($a); ?>
    </div>

  <?php endif; ?>
</main>

<footer>
  <div class="container small">
    <p class="mb-0">© 2025 Espaço Escuta - Todos os direitos reservados.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
