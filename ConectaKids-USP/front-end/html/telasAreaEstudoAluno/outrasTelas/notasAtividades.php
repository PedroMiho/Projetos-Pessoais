<?php
session_start();
include("../../../../back-end/conexao.php");

// Função para verificar login (caso queira reutilizar em outros lugares)
function estaLogado()
{
    return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_tipo']);
}

// Verifica se o usuário está logado e é paciente
if (!estaLogado() || $_SESSION['usuario_tipo'] !== 'paciente') {
    header("Location: ../../telaLogin.php");
    exit();
}

$id_paciente = intval($_SESSION['usuario_id']);

// Consulta notas das atividades (considera nota na tabela entregas_atividades)
$sql = "
  SELECT 
    a.id AS atividade_id,
    a.nome_atividade,
    a.descricao,
    a.data_inicio,
    a.data_encerramento,
    e.id AS entrega_id,
    e.arquivo_entregue,
    e.data_entrega,
    COALESCE(e.nota, a.nota) AS nota
  FROM atividades a
  LEFT JOIN entregas_atividades e
    ON e.atividade_id = a.id AND e.paciente_id = ?
  WHERE a.paciente_id = ?
  ORDER BY a.data_encerramento ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $id_paciente, $id_paciente);
$stmt->execute();
$result = $stmt->get_result();

$atividades = [];
$notas = [];
$labels = [];

while ($row = $result->fetch_assoc()) {
    $atividades[] = $row;
    if (!is_null($row['nota'])) {
        $notas[] = floatval($row['nota']);
        $labels[] = $row['nome_atividade'];
    }
}

// Média geral
$mediaGeral = count($notas) > 0 ? number_format(array_sum($notas) / count($notas), 2) : "0.00";
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Notas das Atividades - ConectaKids</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

  <style>
    body {
      background-color: #fff8f5;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }
    main {
      flex: 1;
    }
    footer {
      background-color: #3e2723;
      color: white;
      padding: 1rem 0;
      text-align: center;
      margin-top: auto;
    }
    .card-plain {
      background-color: #f5f2f0;
      border-radius: 12px;
      border: none;
    }
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
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="../../profissionais.php">Profissionais</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="../../pacientes.php">Pacientes</a>
            </li>
            <li class="nav-item">
              <a class="nav-link text-white fs-5" href="../../telasAreaEstudoAluno/areaEstudoAluno.php">Área de Estudos</a>
            </li>
            <li class="nav-item ms-auto">
              <a class="nav-link text-white d-flex align-items-center fs-5" href="../../telasAreaEstudoAluno/areaEstudoAluno.php">
                <i class="bi bi-person-circle me-1"></i> <?= htmlspecialchars($_SESSION['usuario_nome']); ?>
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- Conteúdo Principal -->
  <main class="container py-5">
    <h2 class="text-center fw-bold mb-3" style="color:#3e2723">
      <i class="bi bi-bar-chart-line me-2"></i>Minhas Notas
    </h2>
    <p class="text-center text-muted mb-5">Visualize suas notas e acompanhe seu progresso nas atividades.</p>

    <!-- Resumo -->
    <div class="row justify-content-center mb-4">
      <div class="col-md-4">
        <div class="card card-plain shadow-sm text-center">
          <div class="card-body">
            <h6 class="text-uppercase fw-bold" style="color:#6d4c41">Média Geral</h6>
            <h2 class="fw-bold" style="color:#3e2723"><?= $mediaGeral ?></h2>
            <p class="small text-muted mb-0"><?= count($notas) ?> atividade(s) avaliadas</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Gráfico -->
    <div class="card card-plain shadow-sm mb-4 p-3">
      <div class="card-body">
        <h5 class="fw-bold text-center mb-3" style="color:#6d4c41">Evolução das Notas</h5>
        <canvas id="graficoNotas" height="90"></canvas>
      </div>
    </div>

    <!-- Histórico -->
    <div class="card card-plain shadow-sm p-3">
      <div class="card-body">
        <h5 class="fw-bold mb-3" style="color:#6d4c41">Histórico de Atividades</h5>
        <div class="table-responsive">
          <table class="table table-striped align-middle">
            <thead style="background-color:#6d4c41; color:white;">
              <tr>
                <th>Atividade</th>
                <th>Início</th>
                <th>Encerramento</th>
                <th>Entrega</th>
                <th>Nota</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php if (count($atividades) === 0): ?>
                <tr>
                  <td colspan="6" class="text-center text-muted">Nenhuma atividade registrada.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($atividades as $a): 
                  $dataInicio = $a['data_inicio'] ? date("d/m/Y", strtotime($a['data_inicio'])) : '-';
                  $dataFim = $a['data_encerramento'] ? date("d/m/Y", strtotime($a['data_encerramento'])) : '-';
                  $entregue = !empty($a['arquivo_entregue']);
                  $nota = $a['nota'] !== null ? number_format(floatval($a['nota']), 1) : '-';
                  $hoje = date('Y-m-d');
                  if ($entregue) $status = "<span class='badge bg-success'>Entregue</span>";
                  elseif ($a['data_encerramento'] < $hoje) $status = "<span class='badge bg-danger'>Vencida</span>";
                  else $status = "<span class='badge bg-warning text-dark'>Em andamento</span>";
                ?>
                <tr>
                  <td><?= htmlspecialchars($a['nome_atividade']) ?></td>
                  <td><?= $dataInicio ?></td>
                  <td><?= $dataFim ?></td>
                  <td>
                    <?php if ($entregue): ?>
                      <a href="<?= htmlspecialchars($a['arquivo_entregue']) ?>" target="_blank" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-file-earmark-check"></i> Ver Entrega
                      </a>
                    <?php else: ?>
                      <span class="text-muted small">—</span>
                    <?php endif; ?>
                  </td>
                  <td><strong><?= $nota ?></strong></td>
                  <td><?= $status ?></td>
                </tr>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </main>

  <!-- Footer -->
  <footer>
    <div class="container small">
      <p class="mb-0">© 2025 ConectaKids - Todos os direitos reservados.</p>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('graficoNotas').getContext('2d');
    const labels = <?= json_encode($labels) ?>;
    const dataNotas = <?= json_encode($notas) ?>;

    new Chart(ctx, {
      type: 'line',
      data: {
        labels: labels,
        datasets: [{
          label: 'Nota',
          data: dataNotas,
          borderColor: '#6d4c41',
          backgroundColor: 'rgba(109, 76, 65, 0.2)',
          tension: 0.3,
          fill: true,
          pointBackgroundColor: '#3e2723'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true, max: 10, ticks: { stepSize: 1 } }
        },
        plugins: { legend: { display: false } }
      }
    });
  </script>
</body>
</html>
