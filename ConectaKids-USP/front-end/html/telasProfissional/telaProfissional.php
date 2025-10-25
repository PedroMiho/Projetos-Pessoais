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

// Busca os dados conforme o tipo de usuário
if ($tipo === 'profissional') {
    $sql = "SELECT nome, sobrenome, telefone, email, descricao, especialidade, foto_perfil, perfil_publico 
            FROM profissionais WHERE id = ?";
} else {
    $sql = "SELECT nome, sobrenome, telefone, email, dificuldade, descricao, foto_perfil, perfil_publico 
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

// Caminho da foto
$foto_path = "uploads/usuario-default.png"; 
if (!empty($usuario['foto_perfil'])) {
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
    html, body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }

    body {
        background-color: #fff8f5;
    }

    main {
        flex: 1;
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

    .fade-out {
        animation: fadeOut 0.5s ease-in-out forwards;
    }

    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; display: none; }
    }
  </style>
</head>

<body>

<!-- Navbar -->
<header>
  <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
    <div class="container-fluid">
      <a class="navbar-brand text-white fw-bold fs-5" href="../../index.php">ConectaKids</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav w-100">
          <li class="nav-item"><a class="nav-link text-white fs-5" href="../profissionais.php">Profissionais</a></li>
          <li class="nav-item"><a class="nav-link text-white fs-5" href="../pacientes.php">Pacientes</a></li>
          <li class="nav-item">
            <?php if ($_SESSION['usuario_tipo'] === 'profissional'): ?>
              <a class="nav-link text-white fs-5" href="../telasAreaEstudo/areaEstudo.php">Área de Estudos</a>
            <?php else: ?>
              <a class="nav-link text-white fs-5" href="../telasAreaEstudoAluno/areaEstudoAluno.php">Área de Estudos</a>
            <?php endif; ?>
          </li>
          <li class="nav-item ms-auto">
            <a class="nav-link text-white fs-5" href="../telasAreaEstudo/logout.php">
              <i class="bi bi-box-arrow-right me-1"></i> Sair
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
</header>

<!-- Conteúdo -->
<main class="py-5">
  <div class="container">

    <!-- Mensagem de atualização -->
    <?php if (isset($_SESSION['mensagem'])): ?>
      <div id="mensagem" class="text-center mb-4">
        <?= $_SESSION['mensagem']; ?>
      </div>
      <?php unset($_SESSION['mensagem']); ?>
    <?php endif; ?>

    <h2 class="fw-bold mb-4 text-center" style="color: #3e2723">Meu Perfil</h2>
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm border-0">
          <div class="card-body">
            <form action="atualizarPerfil.php" method="POST" enctype="multipart/form-data">

              <!-- Foto -->
              <!-- Foto -->
<div class="text-center mb-4">
  <img src="<?= htmlspecialchars($foto_path) ?>" id="fotoPerfilPreview" class="foto-perfil" alt="Foto de perfil">
</div>

<div class="mb-3">
  <label class="form-label fw-bold">Alterar Foto de Perfil</label>
  <input 
      type="file" 
      class="form-control" 
      name="foto" 
      accept="image/*" 
      onchange="previewFoto(event)"
      <?= empty($usuario['foto_perfil']) ? 'required' : '' ?>>
  <?php if (!empty($usuario['foto_perfil'])): ?>
    <small class="text-muted">* Envie uma nova imagem apenas se desejar atualizar sua foto.</small>
  <?php else: ?>
    <small class="text-danger">* Envie uma imagem para completar seu perfil.</small>
  <?php endif; ?>
</div>


              <!-- Informações pessoais -->
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
                <input type="email" class="form-control" value="<?= htmlspecialchars($usuario['email']) ?>" disabled>
              </div>

              <?php if ($tipo === 'profissional'): ?>
                <div class="mb-3">
                  <label class="form-label fw-bold">Especialidade</label>
                  <select class="form-select" name="especialidade" required>
                    <option value="">Selecione sua especialidade</option>
                    <option value="Psicopedagogo" <?= ($usuario['especialidade'] ?? '') === 'Psicopedagogo' ? 'selected' : '' ?>>Psicopedagogo</option>
                    <option value="Neuropsicopedagogo" <?= ($usuario['especialidade'] ?? '') === 'Neuropsicopedagogo' ? 'selected' : '' ?>>Neuropsicopedagogo</option>
                    <option value="Outro" <?= ($usuario['especialidade'] ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                  </select>
                </div>
              <?php else: ?>
                <div class="mb-3">
                  <label class="form-label fw-bold">Dificuldade</label>
                  <select class="form-select" name="dificuldade" required>
                    <option value="">Selecione a dificuldade</option>
                    <option value="TDAH" <?= ($usuario['dificuldade'] ?? '') === 'TDAH' ? 'selected' : '' ?>>TDAH</option>
                    <option value="Dislexia" <?= ($usuario['dificuldade'] ?? '') === 'Dislexia' ? 'selected' : '' ?>>Dislexia</option>
                    <option value="Autismo" <?= ($usuario['dificuldade'] ?? '') === 'Autismo' ? 'selected' : '' ?>>Autismo</option>
                    <option value="Outro" <?= ($usuario['dificuldade'] ?? '') === 'Outro' ? 'selected' : '' ?>>Outro</option>
                  </select>
                </div>
              <?php endif; ?>

              <!-- Descrição -->
              <div class="mb-3">
                <label class="form-label fw-bold">Descrição</label>
                <textarea class="form-control" name="descricao" rows="4" required><?= htmlspecialchars($usuario['descricao'] ?? '') ?></textarea>
              </div>

              <!-- Perfil público -->
              <div class="form-check form-switch mb-4">
                <input class="form-check-input" type="checkbox" name="perfil_publico" id="perfilPublico" value="1"
                       <?= !empty($usuario['perfil_publico']) && $usuario['perfil_publico'] == 1 ? 'checked' : '' ?>>
                <label class="form-check-label fw-bold" for="perfilPublico">Exibir meu perfil publicamente</label>
              </div>

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

<footer class="text-white pt-5 pb-3 mt-auto" style="background-color: #3e2723">
  <div class="container text-center small">
    <p class="mb-0">© 2025 ConectaKids - Todos os direitos reservados.</p>
  </div>
</footer>

<script>
  function previewFoto(event) {
    const foto = document.getElementById('fotoPerfilPreview');
    if (event.target.files && event.target.files[0]) {
      foto.src = URL.createObjectURL(event.target.files[0]);
    }
  }

  setTimeout(() => {
    const msg = document.getElementById('mensagem');
    if (msg) {
      msg.classList.add('fade-out');
      setTimeout(() => msg.remove(), 500);
    }
  }, 3000);
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
