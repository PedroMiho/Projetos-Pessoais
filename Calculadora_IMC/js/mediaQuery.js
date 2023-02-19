const botaoMediaQuery = document.querySelector('[data-botao-media]')

function ativaMenu(evento){
    if(evento.type === "touchstart") evento.preventDefault()
    const nav = document.querySelector('[data-nav]')
    nav.classList.toggle('active')
    const active = nav.classList.contains('active')
    evento.currentTarget.setAttribute('aria-expanded', active)

    if (active) {
        
         evento.currentTarget.setAttribute('aria-label', 'fechar Menu')        
    }
    else{
        evento.currentTarget.setAttribute('aria-label', 'Abrir Menu')
    }
}

botaoMediaQuery.addEventListener('click', ativaMenu)
botaoMediaQuery.addEventListener('touchstart', ativaMenu)
