<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Meu Perfil - ConectaKids</title>
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css"
    rel="stylesheet"
    integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB"
    crossorigin="anonymous"
  />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
  />
</head>
<body>
  <header>
      <nav class="navbar navbar-expand-lg" style="background-color: #6d4c41">
        <div class="container-fluid">
          <a class="navbar-brand text-white fw-bold fs-5" href="#"
            >ConectaKids</a
          >
          <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navbarNav"
            aria-controls="navbarNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
          >
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav w-100">
              <!-- Links à esquerda -->
              <li class="nav-item">
                <a
                  class="nav-link text-white active fs-5"
                  aria-current="page"
                  href="html/profissionais.html"
                  >Profissionais</a
                >
              </li>
              <li class="nav-item">
                <a class="nav-link text-white fs-5" href="html/pacientes.html">Pacientes</a>
              </li>
              <li class="nav-item">
                <a class="nav-link text-white fs-5" href="html/telaLogin.html">Área de Estudos</a>
              </li>
              <!-- Login à direita -->
              <li class="nav-item ms-auto">
                <a
                  class="nav-link text-white d-flex align-items-center fs-5"
                  href="html/telaLogin.html"
                >
                  <i class="bi bi-person-circle me-1"></i> Login
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
    </header>

  <!-- Conteúdo do perfil -->
  <main class="py-5" style="background-color: #fff8f5">
    <div class="container">
      <h2 class="fw-bold mb-4 text-center" style="color: #3e2723">Meu Perfil</h2>

      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card shadow-sm border-0">
            <div class="card-body" style="background-color: #f5f2f0">
              <!-- Foto de perfil -->
              <div class="text-center mb-4">
                <img src="imagens/usuario-default.png" alt="Foto de Perfil" id="fotoPerfil" class="rounded-circle" width="150" height="150">
              </div>

              <!-- Formulário de atualização -->
              <form action="../../back-end/atualizarPerfil.php" method="POST" enctype="multipart/form-data" id="perfilForm">

                <!-- Campos de cadastro/atualização -->
                <div id="profissionalFields">
                  <div class="mb-3">
                    <label class="form-label fw-bold">Nome</label>
                    <input type="text" class="form-control" name="nome" value="João">
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">Sobrenome</label>
                    <input type="text" class="form-control" name="sobrenome" value="Silva">
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">Tipo de Cadastro</label>
                    <select class="form-select" name="tipoCadastro" id="tipoCadastro">
                      <option value="profissional" selected>Profissional</option>
                      <option value="paciente">Paciente</option>
                    </select>
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">Telefone</label>
                    <input type="tel" class="form-control" name="telefone" value="(22) 99999-9999">
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">E-mail</label>
                    <input type="email" class="form-control" name="email" value="joao@email.com" disabled>
                  </div>

                 
                  <!-- Campos extras -->
                  <div class="mb-3">
                    <label class="form-label fw-bold">Alterar Foto de Perfil</label>
                    <input type="file" class="form-control" name="foto" accept="image/*">
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">Especialidade</label>
                    <input type="text" class="form-control" name="especialidade" value="Psicoterapia Infantil">
                  </div>

                  <div class="mb-3">
                    <label class="form-label fw-bold">Descrição</label>
                    <textarea class="form-control" name="descricao" rows="4">Atendimento com escuta ativa, apoio ao desenvolvimento emocional e social.</textarea>
                  </div>
                </div>

             

                <!-- Botão -->
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
      <p class="mb-0">© 2025 Espaço Escuta - Todos os direitos reservados.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
