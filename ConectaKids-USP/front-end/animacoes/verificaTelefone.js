
const telefoneInput = document.querySelector('input[name="telefone"]');
const form = document.querySelector("form");

// Permitir apenas números e limitar a 11 dígitos
telefoneInput.addEventListener("input", (e) => {
// Remove qualquer caractere não numérico
e.target.value = e.target.value.replace(/\D/g, "");

// Limita a 11 caracteres
if (e.target.value.length > 11) {
    e.target.value = e.target.value.slice(0, 11);
}
});

// Validação no envio do formulário
form.addEventListener("submit", (event) => {
const telefone = telefoneInput.value.trim();

if (telefone.length !== 11) {
    event.preventDefault(); // impede o envio ao PHP
    alert("O telefone deve conter exatamente 11 números.");
    telefoneInput.focus();
    return;
}
});

