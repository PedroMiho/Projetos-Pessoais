const cep = document.querySelector('[data-cep]')
const rua = document.querySelector('[data-rua]')
const bairro = document.querySelector('[data-bairro]')
const cidade = document.querySelector('[data-cidade]')
const estado = document.querySelector('[data-estado]')
let verificaoCep = true

cep.addEventListener('focusout', (evento) => {
    buscaEndereço(evento.target.value)
})

async function buscaEndereço(cep){
    var mensagemErro = document.querySelector("[data-cep-valido]")
     mensagemErro.innerHTML = ''

    try {
        let consultaCEP = await fetch(`https://viacep.com.br/ws/${cep}/json/`)
        let consultaCEPJSON = await consultaCEP.json( )

        if(consultaCEPJSON.erro) {
            throw Error ('CEP inexistente')
        }
        verificaoCep = true
        validacoesDeCampos(consultaCEPJSON)
        return consultaCEPJSON
    }   
    catch (erro) {
        mensagemErro.innerHTML = `<p> Insira um CEP valido!! </p>`
        rua.value = ''
        bairro.value = ''
        cidade.value = ''
        estado.value = ''
        verificaoCep = false

        console.log(erro)
    }
}

function validacoesDeCampos(consultaCEP){

    rua.value = consultaCEP.logradouro
    bairro.value = consultaCEP.bairro
    cidade.value = consultaCEP.localidade
    estado.value = consultaCEP.uf
}