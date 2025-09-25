function abrirFormularioEdicao(pagamento){

    const formEdicao = document.querySelector("#formEdicao")
    formEdicao.style.display = "block";
    document.querySelector("#editId").value = pagamento.id;
    console.log(document.querySelector("#editId").value = pagamento.id);
    
    if(pagamento.tipo === "SAIDA"){
        document.querySelector("#campoTipoSaida").style.display = "block"
        document.querySelector("#campoNome").style.display = "none"
        document.querySelector("#tipoSaida").value = pagamento.saida
    }else {
        document.querySelector("#campoTipoSaida").style.display = "none"
        document.querySelector("#campoNome").style.display = "block"
        document.querySelector("#editNome").value = pagamento.nomeCliente
    }

    document.querySelector("#editDescricao").value = pagamento.descricao;
    document.querySelector("#editValor").value = pagamento.valor;
    document.querySelector("#editData").value = pagamento.dataPagamento;

}

function salvarEdicao(){
    const id = document.querySelector("#editId").value
    const nomeCliente = document.querySelector("#editNome").value
    const tipoSaida = document.querySelector("#tipoSaida").value
    const valor = parseFloat(document.querySelector("#editValor").value)
    const descricao = document.querySelector("#editDescricao").value
    const data = document.querySelector("#editData").value
    const tipo = document.querySelector("#filtroTipo").value

    const dadosAtualizados = {
        id: id,
        tipo: tipo,
        descricao: descricao,
        valor: valor,
        data: data
    }
    if (tipo == "ENTRADA"){
        dadosAtualizados.nomeCliente = nomeCliente
    
    }else if (tipo == "SAIDA"){
        dadosAtualizados.saida = tipoSaida
    }

    fetch("http://localhost:8080/pagamentos", {
        method: "PUT", // Define o método HTTP como PUT para atualizar um registro existente
        headers: {
            "Content-Type": "application/json" // Indica que o corpo da requisição está no formato JSON
        },
        body: JSON.stringify(dadosAtualizados) // Converte o objeto JavaScript para uma string JSON
    })
    .then(res => {
        // Verifica se a resposta da API foi bem-sucedida (código de status 200–299)
        if (res.ok) {
            alert("Registro atualizado com sucesso!");
            
            // Oculta o formulário de edição após a atualização
            document.querySelector("#formEdicao").style.display = "none";

            // Recarrega a lista de pagamentos para refletir as alterações
            buscaPagamentos();
        } else {
            alert("Erro ao atualizar.");
        }
    })
    .catch(error => {
        // Trata qualquer erro que ocorra durante a comunicação com o servidor
        console.error("Erro:", error);
        alert("Erro na comunicação com o servidor.");
    });

}


function cancelarEdicao() {
  document.querySelector("#formEdicao").style.display = "none";
}