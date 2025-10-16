<?php
include("conexao.php"); // arquivo com a conexão MySQL

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];

    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $arquivoTmp = $_FILES['foto']['tmp_name'];
        $arquivoNome = time() . '_' . $_FILES['foto']['name']; // evita nomes iguais
        $destino = 'salvaImagens/' . $arquivoNome;

        // Move o arquivo para a pasta uploads
        if (move_uploaded_file($arquivoTmp, $destino)) {
            // Salva no banco o caminho da imagem
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, foto) VALUES (?, ?)");
            $stmt->bind_param("ss", $nome, $arquivoNome);
            $stmt->execute();

            echo "Cadastro realizado com sucesso!";
        } else {
            echo "Erro ao salvar o arquivo!";
        }
    } else {
        echo "Nenhum arquivo enviado!";
    }
}
?>
