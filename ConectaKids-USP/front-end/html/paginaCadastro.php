<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Tela de Cadastro</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" />
  <style>
    body {
      background-color: #efebe9;
      display: flex;
      flex-direction: column;
      min-height: 100vh;
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding: 40px 20px;
    }

    .card {
      max-width: 600px;
      width: 100%;
      background-color: #f5f2f0;
    }

    .hidden {
      display: none;
    }

    .mensagem {
    text-align: center;
    padding: 10px;
    border-radius: 8px;
    margin: 10px auto;
    width: 90%;
    max-width: 400px;
    font-weight: 500;
  }
  .mensagem.sucesso {
    background-color: #c8e6c9;
    color: #256029;
  }
  .mensagem.erro {
    background-color: #ffcdd2;
    color: #b71c1c;
  }
  </style>
</head>

<body>
  <!-- Header -->
  <header>
    <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
      <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold fs-5" href="../index.html">ConectaKids</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
          aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav w-100">
            <li class="nav-item"><a class="nav-link text-white active fs-5" aria-current="page" href="profissionais.php">Profissionais</a></li>
            <li class="nav-item"><a class="nav-link text-white fs-5" href="pacientes.php">Pacientes</a></li>
            <li class="nav-item"><a class="nav-link text-white fs-5" href="telaLogin.php">Área de Estudos</a></li>
            <li class="nav-item ms-auto"><a class="nav-link text-white d-flex align-items-center fs-5" href="telaLogin.php">
                <i class="bi bi-person-circle me-1"></i> Login
              </a></li>
          </ul>
        </div>
      </div>
    </nav>
  </header>

  <!-- Main -->
  <main class="mt-3">
    <div class="card shadow-sm p-4 rounded-4">
      <div class="text-center mb-4">
        <i class="bi bi-person-plus" style="font-size: 4rem; color: #6d4c41"></i>
        <h3 class="fw-bold mt-2" style="color: #3e2723">Cadastro</h3>
      </div>

      <!-- Formulário -->
      <form action="paginaCadastro.php" method="POST" id="cadastroForm">

        <!-- Campos Profissional -->
        <div id="profissionalFields">
          <!-- Campo nome -->
          <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" placeholder="Digite seu nome" name="nome" /></div>
          <!-- Campo sobrenome -->
          <div class="mb-3"><label class="form-label">Sobrenome</label><input type="text" class="form-control" placeholder="Digite seu sobrenome" name="sobrenome" /></div>

          <!-- Tipo de cadastro -->
          <div class="mb-3">
            <label for="tipoCadastro" class="form-label">Tipo de Cadastro</label>
            <select class="form-select" id="tipoCadastro" required name="tipoCadastro">
              <option value="">Selecione...</option>
              <option value="profissional">Profissional</option>
              <option value="paciente">Paciente</option>
            </select>
          </div>
          <!-- Campo Telefone -->
          <div class="mb-3"><label class="form-label">Telefone</label><input type="tel" class="form-control" placeholder="(xx) xxxxx-xxxx" name="telefone" /></div>
          <!-- Campo E-mail -->
          <div class="mb-3"><label class="form-label">E-mail</label><input type="email" class="form-control" placeholder="Digite seu e-mail" name="email" /></div>
          <!-- Campos senha -->
          <div class="mb-3"><label class="form-label">Senha</label><input type="password" class="form-control" placeholder="Digite sua senha" name="senha" /></div>
          <div class="mb-3"><label class="form-label">Confirmar Senha</label><input type="password" class="form-control" placeholder="Confirme sua senha" name="confirma-senha" /></div>
        </div>

        <!-- Termos -->
        <div class="form-check my-3">
          <input type="checkbox" class="form-check-input" id="termos" required />
          <label class="form-check-label" for="termos">Aceito os termos e condições</label>
        </div>

        <!-- Botão -->
        <div class="d-grid">
          <button type="submit" class="btn text-white fw-semibold" style="background-color: #6d4c41">Cadastrar</button>
        </div>
      </form>
    </div>
  </main>

  <script src="../animacoes/verificaSenha.js"></script>
  <script src="../animacoes/verificaTelefone.js"></script>

  <?php if ($_SERVER["REQUEST_METHOD"] == "POST") {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // ativa exceções para erros do MySQL 
    try {
      include("../../back-end/conexao.php");
      $nome = $_POST['nome'];
      $sobrenome = $_POST['sobrenome'];
      $tipoCadastro = $_POST['tipoCadastro'];
      $telefone = $_POST['telefone'];
      $email = $_POST['email'];
      $senha = $_POST['senha'];
      $confirmaSenha = $_POST['confirma-senha'];


      if ($tipoCadastro == "paciente") {
        $sql = "INSERT INTO pacientes (nome, sobrenome, telefone, email, senha) VALUES (?, ?, ?, ?, ?)";
      } else {
        $sql = "INSERT INTO profissionais (nome, sobrenome, telefone, email, senha) VALUES (?, ?, ?, ?, ?)";
      }

      
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("sssss", $nome, $sobrenome, $telefone, $email, $senha);
      $stmt->execute();
      echo "<div class='mensagem sucesso'>Usuário cadastrado com sucesso</div>";
      $stmt->close();
      $conn->close();
    } catch (mysqli_sql_exception $e) {
      if (str_contains($e->getMessage(), 'Duplicate entry')) {
        echo "<div class='mensagem erro'>Este e-mail já está cadastrado.</div>";
      } else {
        echo "<div class='mensagem erro'>Erro ao cadastrar: " . $e->getMessage() . "</div>";
      }
    }
  } ?>



</body>

</html>