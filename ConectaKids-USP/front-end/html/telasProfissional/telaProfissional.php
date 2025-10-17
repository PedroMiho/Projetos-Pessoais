<?php
session_start();
include("../../../back-end/conexao.php");

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id']) || !isset($_SESSION['usuario_tipo'])) {
  header("Location: ../telaLogin.php");
  exit();
}

$id = $_SESSION['usuario_id'];
$tipo = $_SESSION['usuario_tipo'];

// Busca os dados conforme o tipo de usuário (agora incluindo 'foto')
if ($tipo === 'profissional') {
  $sql = "SELECT nome, sobrenome, telefone, email, descricao, especialidade, foto_perfil
          FROM profissionais WHERE id = ?";
} else {
  $sql = "SELECT nome, sobrenome, telefone, email, dificuldade, foto_perfil
          FROM pacientes WHERE id = ?";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
  echo "<div class='alert alert-danger'>Usuário não encontrado!</div>";
  exit();
}

$usuario = $result->fetch_assoc();

// Caminho da foto (corrigido)
$foto_path = "uploads/usuario-default.png"; // imagem padrão

if (!empty($usuario['foto_perfil'])) { // garante que a coluna correta é usada
  $possible = "uploads/" . $usuario['foto_perfil'];
  if (file_exists($possible)) {
    $foto_path = $possible;
  }
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Meu Perfil - ConectaKids</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
  <style>
    body {
      background-color: #fff8f5;
    }

    .foto-perfil {
      width: 150px;
      height: 150px;
      object-fit: cover;
      border-radius: 50%;
      border: 4px solid #6d4c41;
    }

    .card {
      background-color: #f5f2f0;
      border: none;
      border-radius: 15px;
    }
  </style>
</head>

<body>

  <!-- Navbar -->
  <header>
    <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
      <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold fs-5" href="../../index.html">ConectaKids</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav w-100">
            <li class="nav-item"><a class="nav-link text-white fs-5" href="../profissionais.php">Profissionais</a></li>
            <li class="nav-item"><a class="nav-link text-white fs-5" href="../pacientes.php">Pacientes</a></li>
            <li class="nav-item"><a class="nav-link text-white fs-5" href="#">Área de Estudos</a></li>
            <li class="nav-item ms-auto">
              <a class="nav-link text-white fs-5" href="../../logout.php">
                <i class="bi bi-box-arrow-right me-1"></i> Sair
              </a>
            </li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- Conteúdo do Perfil -->
  <main class="py-5">
    <div class="container">
      <h2 class="fw-bold mb-4 text-center" style="color: #3e2723">Meu Perfil</h2>
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card shadow-sm border-0">
            <div class="card-body">
              <form action="atualizarPerfil.php" method="POST" enctype="multipart/form-data">

                <div class="text-center mb-4">
                  <img src="<?= htmlspecialchars($foto_path) ?>" id="fotoPerfilPreview" class="foto-perfil" alt="Foto de perfil">
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold">Alterar Foto de Perfil</label>
                  <input type="file" class="form-control" name="foto" accept="image/*" onchange="previewFoto(event)">
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold">Nome</label>
                  <input type="text" class="form-control" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold">Sobrenome</label>
                  <input type="text" class="form-control" name="sobrenome" value="<?= htmlspecialchars($usuario['sobrenome']) ?>" required>
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold">Telefone</label>
                  <input type="tel" class="form-control" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>">
                </div>

                <div class="mb-3">
                  <label class="form-label fw-bold">E-mail</label>
                  <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
                </div>

                <?php if ($tipo === 'profissional'): ?>
                  <div class="mb-3">
                    <label class="form-label fw-bold">Especialidade</label>
                    <select class="form-select" name="especialidade" required>
                      <option value="">Selecione sua especialidade</option>
                      <option value="Psicopedagogo" <?= ($usuario['especialidade'] ?? '') === 'Psicopedagogo' ? 'selected' : '' ?>>
                        Psicopedagogo
                      </option>
                      <option value="Neuropsicopedagogo" <?= ($usuario['especialidade'] ?? '') === 'Neuropsicopedagogo' ? 'selected' : '' ?>>
                        Neuropsicopedagogo
                      </option>
                    </select>
                  </div>


                  <div class="mb-3">
                    <label class="form-label fw-bold">Descrição</label>
                    <textarea class="form-control" name="descricao" rows="4"><?= htmlspecialchars($usuario['descricao'] ?? '') ?></textarea>
                  </div>
                <?php else: ?>
                  <div class="mb-3">
                    <label class="form-label fw-bold">Dificuldade</label>
                    <input type="text" class="form-control" name="dificuldade" value="<?= htmlspecialchars($usuario['dificuldade'] ?? '') ?>">
                  </div>
                <?php endif; ?>

                <!-- campo oculto para enviar id e tipo ao atualizar (útil no atualizarPerfil.php) -->
                <input type="hidden" name="usuario_id" value="<?= htmlspecialchars($id) ?>">
                <input type="hidden" name="usuario_tipo" value="<?= htmlspecialchars($tipo) ?>">

                <div class="d-grid">
                  <button type="submit" class="btn text-white fw-semibold" style="background-color: #6d4c41">Salvar Alterações</button>
                </div>

              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <footer class="text-white pt-5 pb-3" style="background-color: #3e2723">
    <div class="container text-center small">
      <p class="mb-0">© 2025 ConectaKids - Todos os direitos reservados.</p>
    </div>
  </footer>

  <script>
    // Preview da imagem antes de enviar
    function previewFoto(event) {
      const foto = document.getElementById('fotoPerfilPreview');
      if (event.target.files && event.target.files[0]) {
        foto.src = URL.createObjectURL(event.target.files[0]);
      }
    }
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>