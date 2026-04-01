function showError(inputId, message) {
  const input = document.getElementById(inputId);
  if (!input) return;
  input.style.borderColor = "#e53935";
  let errorEl = document.getElementById("error-" + inputId);
  if (!errorEl) {
    errorEl = document.createElement("span");
    errorEl.id = "error-" + inputId;
    errorEl.style.cssText = "color:#e53935; font-size:12px; margin-bottom:6px; display:block;";
    input.insertAdjacentElement("afterend", errorEl);
  }
  errorEl.textContent = message;
}

function clearError(inputId) {
  const input = document.getElementById(inputId);
  if (input) input.style.borderColor = "#d4d4d4";
  const errorEl = document.getElementById("error-" + inputId);
  if (errorEl) errorEl.remove();
}

function clearAllErrors(ids) {
  ids.forEach(id => clearError(id));
}

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidUsername(username) {
  return /^[a-zA-Z0-9_-]{3,20}$/.test(username);
}

function isValidPassword(password) {
  return password.length >= 8 &&
    /[A-Z]/.test(password) &&
    /[0-9]/.test(password);
}

function validarLogin() {
  clearAllErrors(["usuario", "senha"]);

  const usuario = document.getElementById("usuario").value.trim();
  const senha   = document.getElementById("senha").value.trim();
  let valid = true;

  if (usuario === "") {
    showError("usuario", "Por favor, insira seu usuário.");
    valid = false;
  }
  if (senha === "") {
    showError("senha", "Por favor, insira sua senha.");
    valid = false;
  }

  return valid;
}

function validarCadastro() {
  clearAllErrors(["novoUsuario", "email", "novaSenha", "confirmarSenha"]);

  const novoUsuario    = document.getElementById("novoUsuario").value.trim();
  const email          = document.getElementById("email").value.trim();
  const novaSenha      = document.getElementById("novaSenha").value.trim();
  const confirmarSenha = document.getElementById("confirmarSenha").value.trim();
  let valid = true;

  if (novoUsuario === "") {
    showError("novoUsuario", "Por favor, insira um usuário.");
    valid = false;
  } else if (!isValidUsername(novoUsuario)) {
    showError("novoUsuario", "3–20 caracteres. Apenas letras, números, _ e -.");
    valid = false;
  }

  if (email === "") {
    showError("email", "Por favor, insira um e-mail.");
    valid = false;
  } else if (!isValidEmail(email)) {
    showError("email", "Insira um e-mail válido (ex: nome@dominio.com).");
    valid = false;
  }

  if (novaSenha === "") {
    showError("novaSenha", "Por favor, crie uma senha.");
    valid = false;
  } else if (!isValidPassword(novaSenha)) {
    showError("novaSenha", "Mínimo 8 caracteres, uma letra maiúscula e um número.");
    valid = false;
  }

  if (confirmarSenha === "") {
    showError("confirmarSenha", "Por favor, confirme sua senha.");
    valid = false;
  } else if (novaSenha !== confirmarSenha) {
    showError("confirmarSenha", "As senhas não coincidem.");
    valid = false;
  }

  return valid;
}