const botoes = document.querySelectorAll('[data-botao]')
const alturaInvalido = document.querySelector("[data-valor-altura]")
const pesoInvalido = document.querySelector("[data-valor-peso]")
const valorIMC  = document.querySelector("[data-imc]")  

botoes.forEach((elemento) => {
    elemento.addEventListener('click', (evento) => {
        evento.preventDefault()
        pegaValores(evento.target.value)
    })
})

function pegaValores(botao){
    const peso = document.querySelector('[data-peso]')
    const altura = document.querySelector('[data-altura]')

    alturaInvalido.innerHTML = ''
    pesoInvalido.innerHTML = ''

    if(botao == 'Limpar'){
        peso.value = ''
        altura.value = ''
        valorIMC.innerHTML = 'SEU IMC É DE'
        const linhasTabela = document.querySelectorAll('[data-linha]')
        linhasTabela.forEach(elemento => elemento.style.backgroundColor = "white")
    }

    else{
        manipulaValores(altura, peso)
    } 
}

function manipulaValores(altura, peso){
    
    const alturaNumero = Number(altura.value)
    const pesoNumero = Number(peso.value)

    let vertificacaoAltura = true 
    let vertificacaoPeso = true 
 
    if (alturaNumero >= 3 || alturaNumero <= 0 || isNaN(alturaNumero)){
        alturaInvalido.innerHTML = `Insira um valor valido`
        vertificacaoAltura = false
    }

    if(pesoNumero >= 300 || pesoNumero <= 0 || isNaN(pesoNumero)){
        pesoInvalido.innerHTML = `Insira um valor valido`
        vertificacaoPeso = false
    } 

    if(vertificacaoAltura && vertificacaoPeso) {       
        let IMC = pesoNumero / (alturaNumero**2)
        valorIMC.innerHTML = `SEU IMC É DE ${IMC.toFixed(2)}`
        completaTabela(IMC.toFixed(2))
    }
}

function completaTabela(IMC){
    // const pedro = document.querySelector('[data-tabela=magreza]')
    // console.log(pedro)
    const dadosTabela = document.querySelectorAll('[data-tabela]')

    dadosTabela.forEach((elemento) => {
        elemento.parentNode.style.backgroundColor = "white"
        if(IMC < 18.5 && elemento.innerHTML === 'MAGREZA'){
            elemento.parentNode.style.backgroundColor = "aqua"
        }
        if(IMC >= 18.5 && IMC < 25 && elemento.innerHTML === 'NORMAL'){  
            elemento.parentNode.style.backgroundColor = "aqua"
        }
        if(IMC >= 25 &&  IMC < 30 && elemento.innerHTML === 'SOBREPESO'){
            elemento.parentNode.style.backgroundColor = "aqua"
        }
        if(IMC >= 30 && IMC < 40 && elemento.innerHTML === 'OBESIDADE'){
            elemento.parentNode.style.backgroundColor = "aqua"
        }
        if(IMC >= 40 && elemento.innerHTML === 'OBESIDADE GRAVE'){
            elemento.parentNode.style.backgroundColor = "aqua"
        }
    })
}