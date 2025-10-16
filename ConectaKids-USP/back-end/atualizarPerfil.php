<?php if ($_SERVER["REQUEST_METHOD"] == "POST") {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // ativa exceções para erros do MySQL 
    try {
      include("../../back-end/conexao.php");
      $nome = $_POST['nome'];
      $sobrenome = $_POST['sobrenome'];
      $tipoCadastro = $_POST['tipoCadastro'];
      $telefone = $_POST['telefone'];
      $email = $_POST['email'];
    //   $foto = foto
        $descricao = $_POST['descricao'];
        $tipoProfissional = $_POST['tipoProfissional'];



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