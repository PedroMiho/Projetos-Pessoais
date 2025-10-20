const alfabetoDiv = document.getElementById('alfabeto');
const letras = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ'.split('');

// Cria os botões para cada letra
letras.forEach(letra => {
  const btn = document.createElement('div');
  btn.classList.add('letra');
  btn.textContent = letra;
  btn.addEventListener('click', () => tocarLetra(letra));
  alfabetoDiv.appendChild(btn);
});

// Função para tocar o som da letra
function tocarLetra(letra) {
  const audio = new Audio(`sounds/${letra}.mp3`);
  audio.play();

  // Animação visual
  const btn = [...document.getElementsByClassName('letra')]
    .find(b => b.textContent === letra.toUpperCase());
  if (btn) {
    btn.classList.add('active');
    setTimeout(() => btn.classList.remove('active'), 200);
  }
}

// Detecta letras digitadas no teclado
document.addEventListener('keydown', (e) => {
  const letra = e.key.toUpperCase();
  if (letras.includes(letra)) {
    tocarLetra(letra);
  }
});
