<?php
session_start();
include("../../../../back-end/conexao.php");

// Verifica se o usuário está logado e é paciente
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_tipo'] !== 'paciente') {
    header("Location: ../../telaLogin.php");
    exit();
}

$id_paciente = $_SESSION['usuario_id'];

// Busca todas as notas das atividades do paciente
$sql = "SELECT a.nome_atividade, a.nota, a.data_encerramento
        FROM atividades a
        WHERE a.paciente_id = ? AND a.nota IS NOT NULL
        ORDER BY a.data_encerramento ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_paciente);
$stmt->execute();
$result = $stmt->get_result();

$atividades = [];
$notas = [];
$datas = [];

while ($row = $result->fetch_assoc()) {
    $atividades[] = $row['nome_atividade'];
    $notas[] = floatval($row['nota']);
    $datas[] = date("d/m/Y", strtotime($row['data_encerramento']));
}

// Calcula média geral
$mediaGeral = count($notas) > 0 ? number_format(array_sum($notas) / count($notas), 2) : "0.00";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notas das Atividades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body style="background-color: #fff8f5;">
    <div class="container py-5">
        <h2 class="text-center fw-bold mb-4" style="color: #3e2723;">
            <i class="bi bi-bar-chart-line me-2"></i>Notas das Atividades
        </h2>
        <p class="text-center text-muted mb-5">
            Visualize suas notas e acompanhe seu progresso em cada atividade.
        </p>

        <!-- Resumo -->
        <div class="row justify-content-center mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 text-center" style="background-color: #f5f2f0;">
                    <div class="card-body">
                        <h5 class="fw-bold" style="color: #6d4c41;">Média Geral</h5>
                        <h2 class="fw-bold" style="color: #3e2723;"><?php echo $mediaGeral; ?></h2>
                        <p class="text-muted mb-0">Baseado em <?php echo count($notas); ?> atividades</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gráfico -->
        <div class="card shadow-sm border-0 mb-5" style="background-color: #f5f2f0;">
            <div class="card-body">
                <h5 class="fw-bold text-center mb-3" style="color: #6d4c41;">Evolução das Notas</h5>
                <canvas id="graficoNotas" height="100"></canvas>
            </div>
        </div>

        <!-- Tabela -->
        <div class="card shadow-sm border-0" style="background-color: #f5f2f0;">
            <div class="card-body">
                <h5 class="fw-bold mb-3" style="color: #6d4c41;">Histórico de Atividades</h5>
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr style="background-color: #6d4c41; color: #fff;">
                                <th>Atividade</th>
                                <th>Data de Encerramento</th>
                                <th>Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($atividades) > 0): ?>
                                <?php for ($i = 0; $i < count($atividades); $i++): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($atividades[$i]); ?></td>
                                        <td><?php echo htmlspecialchars($datas[$i]); ?></td>
                                        <td><strong><?php echo htmlspecialchars($notas[$i]); ?></strong></td>
                                    </tr>
                                <?php endfor; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Nenhuma nota disponível no momento.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('graficoNotas').getContext('2d');
        const graficoNotas = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($atividades); ?>,
                datasets: [{
                    label: 'Nota',
                    data: <?php echo json_encode($notas); ?>,
                    borderColor: '#6d4c41',
                    backgroundColor: 'rgba(109, 76, 65, 0.2)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true,
                    pointBackgroundColor: '#3e2723'
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        ticks: { stepSize: 1 }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#3e2723',
                        titleColor: '#fff',
                        bodyColor: '#fff'
                    }
                }
            }
        });
    </script>
</body>
</html>
