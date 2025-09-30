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
  </style>
</head>
<body>
  <!-- Header -->
  <header>
    <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
      <div class="container-fluid">
        <a class="navbar-brand text-white fw-bold fs-5" href="#">ConectaKids</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav w-100">
            <li class="nav-item"><a class="nav-link text-white active fs-5" aria-current="page" href="profissionais.html">Profissionais</a></li>
            <li class="nav-item"><a class="nav-link text-white fs-5" href="pacientes.html">Pacientes</a></li>
            <li class="nav-item"><a class="nav-link text-white fs-5" href="telaLogin.html">Área de Estudos</a></li>
            <li class="nav-item ms-auto"><a class="nav-link text-white d-flex align-items-center fs-5" href="telaLogin.html">
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
        <!-- Tipo de cadastro -->
        <div class="mb-3">
          <label for="tipoCadastro" class="form-label fw-semibold">Tipo de Cadastro</label>
          <select class="form-select" id="tipoCadastro" required name="tipoCadastro">
            <option value="">Selecione...</option>
            <option value="profissional">Profissional</option>
            <option value="paciente">Paciente</option>
          </select>
        </div>

        <!-- Campos Profissional -->
        <div id="profissionalFields" class="hidden">
          <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" placeholder="Digite seu nome" name="nome" /></div>
          <div class="mb-3"><label class="form-label">Sobrenome</label><input type="text" class="form-control" placeholder="Digite seu sobrenome" name="sobrenome" /></div>
          <div class="mb-3"><label class="form-label">Área de atuação</label>
            <select class="form-select" name="areaAtuacao">
              <option value="">Selecione...</option>
              <option value="psicopedagogo">Psicopedagogo</option>
              <option value="neuropsicopedagogo">Neuropsicopedagogo</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Telefone</label><input type="tel" class="form-control" placeholder="(xx) xxxxx-xxxx" name="telefone" /></div>
          <div class="mb-3"><label class="form-label">E-mail</label><input type="email" class="form-control" placeholder="Digite seu e-mail" name="email" /></div>
          <div class="mb-3"><label class="form-label">Senha</label><input type="password" class="form-control" placeholder="Digite sua senha" name="senha" /></div>
          <div class="mb-3"><label class="form-label">Confirmar Senha</label><input type="password" class="form-control" placeholder="Confirme sua senha" name="confirma-senha" /></div>
        </div>

        <!-- Campos Paciente -->
        <div id="pacienteFields" class="hidden">
          <div class="mb-3"><label class="form-label">Nome</label><input type="text" class="form-control" placeholder="Digite seu nome" name="nome" /></div>
          <div class="mb-3"><label class="form-label">Sobrenome</label><input type="text" class="form-control" placeholder="Digite seu sobrenome" name="sobrenome" /></div>
          <div class="mb-3"><label class="form-label">Dificuldade</label>
            <select class="form-select" name="dificuldade">
              <option value="">Selecione...</option>
              <option value="tdah">TDAH</option>
              <option value="tea">TEAs</option>
              <option value="dislexia">Dislexia</option>
              <option value="disgrafia">Disgrafia/Disortografia</option>
              <option value="discalculia">Discalculia</option>
            </select>
          </div>
          <div class="mb-3"><label class="form-label">Telefone</label><input type="tel" class="form-control" placeholder="(xx) xxxxx-xxxx" name="telefone" /></div>
          <div class="mb-3"><label class="form-label">E-mail</label><input type="email" class="form-control" placeholder="Digite seu e-mail" name="email" /></div>
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

  <script>
    const tipoCadastro = document.getElementById("tipoCadastro");
    const profissionalFields = document.getElementById("profissionalFields");
    const pacienteFields = document.getElementById("pacienteFields");

    function toggleFields() {
      // Oculta ambos os blocos
      profissionalFields.classList.add("hidden");
      pacienteFields.classList.add("hidden");

      // Desabilita todos os campos inicialmente
      profissionalFields.querySelectorAll("input, select").forEach(el => { el.disabled = true; el.required = false; });
      pacienteFields.querySelectorAll("input, select").forEach(el => { el.disabled = true; el.required = false; });

      // Mostra e habilita o bloco selecionado
      if (tipoCadastro.value === "profissional") {
        profissionalFields.classList.remove("hidden");
        profissionalFields.querySelectorAll("input, select").forEach(el => { el.disabled = false; el.required = true; });
      } else if (tipoCadastro.value === "paciente") {
        pacienteFields.classList.remove("hidden");
        pacienteFields.querySelectorAll("input, select").forEach(el => { el.disabled = false; el.required = true; });
      }
    }

    tipoCadastro.addEventListener("change", toggleFields);
    window.addEventListener("load", toggleFields);
  </script>

<?php
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    try {
      include("../back-end/conexao.php");
      $tipoCadastro = $_POST["tipoCadastro"];
      $nome = $_POST["nome"];
      $sobrenome = $_POST["sobrenome"];
      $areaAtuacao = $_POST["areaAtuacao"] ?? null;
      $dificuldade = $_POST["dificuldade"] ?? null;
      $telefone = $_POST["telefone"];
      $email = $_POST["email"];
      $senha = $_POST["senha"];
      $confirmaSenha = $_POST["confirma-senha"];

      // Validação de senha
      if ($senha !== $confirmaSenha) {
        echo "<div class='alert alert-danger mt-3' role='alert'>
                As senhas não coincidem. Por favor, tente novamente.
              </div>";
      } else {
        // Aqui você pode inserir os dados no banco
        echo "<div class='alert alert-success mt-3' role='alert'>
                Cadastro realizado com sucesso!<br>
                Tipo: $tipoCadastro | Nome: $nome $sobrenome | Área: $areaAtuacao | Dificuldade: $dificuldade | Telefone: $telefone | Email: $email
              </div>";
      }
    } catch (mysqli_sql_exception $e) {
      if (str_contains($e->getMessage(), 'Duplicate entry')) {
        echo "<div class='alert alert-danger mt-3' role='alert'>
                Este e-mail já está cadastrado.
              </div>";
      } else {
        echo "<div class='alert alert-danger mt-3' role='alert'>
                Erro ao cadastrar: " . $e->getMessage() . "
              </div>";
      }
    }
  }
?>

</body>
</html>
