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
    SELECT a.id, a.nome_atividade, a.descricao, a.data_inicio, a.data_encerramento, 
           e.arquivo_entregue, e.data_entrega
    FROM atividades a
    LEFT JOIN entregas_atividades e ON e.atividade_id = a.id AND e.paciente_id = a.paciente_id
    WHERE a.profissional_id = ? AND a.paciente_id = ?
  ";
  $stmtAtividades = $conn->prepare($sqlAtividades);
  $stmtAtividades->bind_param("ii", $profissional_id, $aluno_id);
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
    body { background-color: #f7f3f0; display: flex; flex-direction: column; min-height: 100vh; }
    main { flex: 1; }
    footer { background-color: #3e2723; color: white; padding: 1.5rem 0; margin-top: auto; text-align: center; }
    .foto-aluno { width: 100px; height: 100px; object-fit: cover; border-radius: 50%; border: 3px solid #6d4c41; }
    .card-aluno:hover { transform: scale(1.05); cursor: pointer; }
    .section-title { background-color: #6d4c41; color: white; padding: 10px; border-radius: 10px; }
    .card-atividade { border-radius: 10px; border-left: 6px solid #6d4c41; margin-bottom: 10px; }
  </style>
</head>
<body>

<!-- Header -->
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

<!-- Main -->
<main class="container py-5">
  <h2 class="text-center mb-4" style="color: #3e2723;">Acompanhamento de Atividades</h2>

  <!-- Se nenhum aluno for selecionado -->
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

    <!-- Se o aluno foi selecionado -->
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
      <a href="acompanhamentoAtividades.php" class="btn btn-outline-secondary mt-2">Voltar</a>
    </div>

    <!-- Seções de atividades -->
    <?php
      $entregues = [];
      $andamento = [];
      $vencidas = [];
      $dataAtual = date('Y-m-d');

      while ($atv = $atividades->fetch_assoc()) {
        if (!empty($atv['arquivo_entregue'])) {
          $entregues[] = $atv;
        } elseif ($atv['data_encerramento'] < $dataAtual) {
          $vencidas[] = $atv;
        } else {
          $andamento[] = $atv;
        }
      }

      function renderAtividade($atv, $entregue = false) {
        $dataInicio = date('d/m/Y', strtotime($atv['data_inicio']));
        $dataFim = date('d/m/Y', strtotime($atv['data_encerramento']));
        echo "<div class='card card-atividade p-3'>
                <h5>{$atv['nome_atividade']}</h5>
                <p class='text-muted small'>{$atv['descricao']}</p>
                <p class='mb-1'><strong>Início:</strong> $dataInicio | <strong>Encerramento:</strong> $dataFim</p>";
        if ($entregue) {
          echo "<a href='../../../../{$atv['arquivo_entregue']}' target='_blank' class='btn btn-outline-success btn-sm mt-2'>
                  <i class='bi bi-file-earmark-pdf'></i> Ver PDF Entregue
                </a>";
        }
        echo "</div>";
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

<!-- Footer -->
<footer>
  <div class="container small">
    <p class="mb-0">© 2025 Espaço Escuta - Todos os direitos reservados.</p>
  </div>
</footer>

</body>
</html>
