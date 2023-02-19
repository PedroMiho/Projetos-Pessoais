const formulario = document.querySelector("[data-formulario]")

formulario.addEventListener('submit', (evento) => {
    evento.preventDefault()

    if(verificacaoCPF == false || verificaoCep == false){
        window.alert('Reveja os Dados inseridos')
    }
    else{
        window.location.href = '../pages/cadastroConcluido.html'
    }

})