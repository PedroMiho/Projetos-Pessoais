const cpf = document.querySelector("[data-cpf]")
const cpfInvalido = document.querySelector("[data-cpf-valido]")
let verificacaoCPF = true

cpf.addEventListener('focusout', (evento) => {
    evento.preventDefault()
    validaCpf(evento.target.value)
})

function validaCpf(cpf){

    if(cpf.length < 11){
        cpfInvalido.innerHTML = 'Insira um CPF valido'
        verificacaoCPF = false
    }

    else{
              
        if (validaNumerosRepetidos(cpf) || validaPrimeiroDigito(cpf) || validaSegundoDigito(cpf) ) {
            cpfInvalido.innerHTML = 'Insira um CPF valido'
            verificacaoCPF = false
        }
        else{
            cpfInvalido.innerHTML = ''
            verificacaoCPF = true
        }
    }

}



function validaNumerosRepetidos(cpf){
    const numerosRepetidos = [
        '00000000000',
        '11111111111',
        '22222222222',
        '33333333333',
        '44444444444',
        '55555555555',
        '66666666666',
        '77777777777',
        '88888888888',
        '99999999999'
    ]

    return numerosRepetidos.includes(cpf)
}

function validaPrimeiroDigito(cpf){
    let soma = 0
    let multiplicador = 10

    for (let i = 0;  i < 9; i++){
        soma += cpf[i] * multiplicador
        multiplicador--
    }

    soma = (soma * 10) % 11

    if (soma == 10 || soma == 11){
        soma = 0
    }

    return soma != cpf[9]

}

function validaSegundoDigito(cpf){

    let soma = 0
    let multiplicador = 11

    for (let i = 0;  i < 10; i++){
        soma += cpf[i] * multiplicador
        multiplicador--
    }

    soma = (soma * 10) % 11

    if (soma == 10 || soma == 1){
        soma = 0
    }

    return soma != cpf[10]
}
