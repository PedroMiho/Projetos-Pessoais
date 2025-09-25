let tabela = document.querySelector("#tabelaPagamentos")

function buscaPagamentos(){
    let tipo = document.querySelector("#filtroTipo").value

    exibeCabecalhoPagamentos(tipo)
    fetch(`http://localhost:8080/pagamentos/tipo/${tipo}`)
    .then(res => res.json())
    .then(data => {
        let dadosPagamentos = data;
        exibeTabelaPagamentos(dadosPagamentos);
        
    })    
}

function exibeCabecalhoPagamentos(tipo){
    let cabecalhoNomeCliente = document.querySelector("#thNomeCliente")
    let cabecalhoTipoSaida = document.querySelector("#thSaida")
    if (tipo === "ENTRADA") {
        cabecalhoNomeCliente.style.display = "block"
        cabecalhoTipoSaida.style.display = "none"
    }
    else if (tipo === "SAIDA") {
        cabecalhoNomeCliente.style.display = "none"
        cabecalhoTipoSaida.style.display = "block"
    }
}

function exibeTabelaPagamentos(dadosPagamentos){
    //Formatar a tabela a cada atualização do tipo de pagamento
    tabela.innerHTML = ""
    
    dadosPagamentos.forEach(pagamentos => {
    
    //Criando as tags para a tabela 
    let pagamentoTr = document.createElement("tr")
    let tipoPagamentoTd = document.createElement("td")
    let nomeClienteTipoSaidaTd = document.createElement("td")
    let descricaoTd = document.createElement("td")
    let dataTd = document.createElement("td")
    let valorTd = document.createElement("td")
    let acoesTd = document.createElement("td") //Cria a célula de ações

    
    //Adicionando conteudo nas tags
    tipoPagamentoTd.textContent = pagamentos.tipo
    if (pagamentos.tipo == "ENTRADA"){
        nomeClienteTipoSaidaTd.textContent = pagamentos.nomeCliente
    }else if (pagamentos.tipo == "SAIDA"){
        nomeClienteTipoSaidaTd.textContent = pagamentos.saida
    }
    descricaoTd.textContent = pagamentos.descricao
    dataTd.textContent = pagamentos.dataPagamento
    valorTd.textContent = "R$ " + parseFloat(pagamentos.valor).toFixed(2)

    //Adicionando todas as células na linha
    pagamentoTr.appendChild(tipoPagamentoTd)
    pagamentoTr.appendChild(nomeClienteTipoSaidaTd)
    pagamentoTr.appendChild(descricaoTd)
    pagamentoTr.appendChild(dataTd)
    pagamentoTr.appendChild(valorTd)
    
    // Criando o botão de editar pagamento
    let btnEditar = document.createElement("button") // Cria um botão HTML
    btnEditar.textContent = "Editar" // Define o texto do botão
    btnEditar.className = "btn-editar" // Define uma classe CSS para estilização
    btnEditar.dataset.id = pagamentos.id // Armazena o ID do pagamento como atributo data-* (útil para identificar qual registro será editado)

    //Adicionando o evento de clique no botão de editar
    btnEditar.addEventListener("click", function(){
        abrirFormularioEdicao(pagamentos)
    })

    // Criando o botão de excluir pagamento
    let btnExcluir = document.createElement("button") // Cria outro botão HTML
    btnExcluir.textContent = "Excluir" // Define o texto do botão
    btnExcluir.className = "btn-excluir" // Define uma classe CSS para estilização
    btnExcluir.dataset.id = pagamentos.id // Armazena o ID do pagamento como atributo data-* (útil para identificar qual registro será excluído)

    //Adicionando o evento de clique no botão de excluir
    btnExcluir.addEventListener("click", function(){
        excluirPagamento(pagamentos.id)
    })    

    // Adicionando os botões à célula de ações
    acoesTd.appendChild(btnEditar) // Insere o botão de editar na célula
    acoesTd.appendChild(btnExcluir) // Insere o botão de excluir na célula

    // Adicionando a célula de ações à linha da tabela
    pagamentoTr.appendChild(acoesTd) // Adiciona a célula com os botões à linha da tabela

    tabela.appendChild(pagamentoTr)
    })
    


    
}