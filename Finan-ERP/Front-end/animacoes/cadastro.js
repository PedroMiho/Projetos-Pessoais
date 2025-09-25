const form = document.getElementById('formPagamento');

form.addEventListener('submit', function(event) {
    event.preventDefault();

    const tipo = document.querySelector("#tipo").value
    
    const nomeCliente = document.querySelector("#nomeCliente").value
    const tipoSaida = document.querySelector("#tipoSaida").value
    const descricao = document.querySelector("#descricao").value
    const data = document.querySelector("#dataPagamento").value
    const valor = parseFloat(document.querySelector("#valor").value)
    console.log(tipo,nomeCliente,tipoSaida,descricao, data, valor);
    

    const dados = {
        tipo: tipo,
        descricao: descricao, 
        dataPagamento: data,
        valor:valor
    };

    if (tipo === "ENTRADA") {
        console.log(nomeCliente);
        
        dados.nomeCliente = nomeCliente;
        
    } else if (tipo === "SAIDA") {
        dados.saida = tipoSaida;
    }
    
    fetch('http://localhost:8080/pagamentos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(dados)
    })
    .then(response => {
        console.log(dados);
        
        const mensagem = document.getElementById('mensagem');
        if (response.ok) {
            mensagem.innerText = "✅ Dados cadastrados com sucesso!";
            mensagem.classList.remove('text-danger');
            mensagem.classList.add('text-success');
            form.reset();
        } else {
            mensagem.innerText = "❌ Erro ao cadastrar dados.";
            mensagem.classList.remove('text-success');
            mensagem.classList.add('text-danger');
        }
    })
    .catch(error => {
        const mensagem = document.getElementById('mensagem');
        mensagem.innerText = "❌ Erro na requisição: " + error.message;
        mensagem.classList.remove('text-success');
        mensagem.classList.add('text-danger');
    });
    console.log(dados);
})