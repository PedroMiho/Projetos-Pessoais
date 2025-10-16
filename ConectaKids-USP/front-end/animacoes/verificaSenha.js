document.addEventListener("DOMContentLoaded", () => {
  const form = document.querySelector("form"); // Seleciona o formulário
  const senhaInput = document.querySelector('input[name="senha"]');
  const confirmaSenhaInput = document.querySelector('input[name="confirma-senha"]');

  const senhaMsg = document.createElement("small");
  senhaMsg.style.display = "block";
  senhaInput.parentNode.appendChild(senhaMsg);

  const confirmaMsg = document.createElement("small");
  confirmaMsg.style.display = "block";
  confirmaSenhaInput.parentNode.appendChild(confirmaMsg);

  function validarSenha(senha) {
    const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*()_+{}\[\]:;<>,.?~\\-])(?=.{8,})/;
    return regex.test(senha);
  }

  senhaInput.addEventListener("input", () => {
    const senha = senhaInput.value;

    if (senha.length === 0) {
      senhaMsg.textContent = "";
    } else if (!validarSenha(senha)) {
      senhaMsg.textContent = "A senha deve ter pelo menos 8 caracteres, 1 maiúscula, 1 minúscula e 1 símbolo.";
      senhaMsg.style.color = "red";
    } else {
      senhaMsg.textContent = "Senha forte ✅";
      senhaMsg.style.color = "green";
    }
  });

  confirmaSenhaInput.addEventListener("input", () => {
    const senha = senhaInput.value;
    const confirma = confirmaSenhaInput.value;

    if (confirma.length === 0) {
      confirmaMsg.textContent = "";
    } else if (senha !== confirma) {
      confirmaMsg.textContent = "As senhas não coincidem.";
      confirmaMsg.style.color = "red";
    } else {
      confirmaMsg.textContent = "As senhas coincidem ✅";
      confirmaMsg.style.color = "green";
    }
  });

  // 🔒 Validação final antes de enviar ao PHP
  form.addEventListener("submit", (event) => {
    const senha = senhaInput.value;
    const confirma = confirmaSenhaInput.value;

    if (!validarSenha(senha)) {
      event.preventDefault(); // Impede o envio
      alert("A senha não atende aos critérios de segurança.");
      senhaInput.focus();
      return;
    }

    if (senha !== confirma) {
      event.preventDefault();
      alert("As senhas não coincidem.");
      confirmaSenhaInput.focus();
      return;
    }

    // ✅ Se tudo estiver certo, o formulário será enviado normalmente ao PHP
  });
});
