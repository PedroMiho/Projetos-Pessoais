function mostrarCamposAdicionais(){
    const tipo  = document.getElementById('tipo').value
    const campoNome = document.getElementById('campoNome')
    const campoSaida = document.getElementById('campoTipoSaida')

    // Campos de entrada
    const inputNomeCliente = document.getElementById('nomeCliente');
    const selectTipoSaida = document.getElementById('tipoSaida');

    // Zera os valores ao trocar o tipo
    inputNomeCliente.value = '';
    selectTipoSaida.value = '';

    if (tipo === "ENTRADA"){
        campoNome.style.display = 'block'
        campoSaida.style.display = 'none'      
    }
    else if (tipo === "SAIDA"){
        campoNome.style.display = 'none'
        campoSaida.style.display = 'block'
    }
    else {
        campoNome.style.display = 'none'
        campoSaida.style.display = 'none'
    }
    
}