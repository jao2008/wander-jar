document.addEventListener("DOMContentLoaded", () => {
  initPasswordToggles();
  initResetPasswordValidation();
  initAuthEntryAnimation();
});

function initPasswordToggles() {
  const toggles = document.querySelectorAll(".password-toggle");

  toggles.forEach((toggle) => {
    toggle.addEventListener("click", (event) => {
      event.preventDefault();

      const wrapper = toggle.closest(".password-wrapper");
      const input = wrapper?.querySelector(".form-input");
      const icon = toggle.querySelector("i");

      if (!input || !icon) {
        return;
      }

      const isHidden = input.type === "password";

      input.type = isHidden ? "text" : "password";

      icon.classList.toggle("bi-eye", isHidden);
      icon.classList.toggle("bi-eye-slash", !isHidden);

      toggle.setAttribute(
        "aria-label",
        isHidden ? "Esconder palavra-passe" : "Mostrar palavra-passe"
      );

      toggle.classList.add("is-pressed");

      window.setTimeout(() => {
        toggle.classList.remove("is-pressed");
      }, 120);

      input.focus();
    });
  });
}

function initResetPasswordValidation() {
  const form = document.querySelector(".auth-form");

  if (!form) {
    return;
  }

  const emailInput = form.querySelector('input[type="email"], input[name="email"]');
  const passwordInput = form.querySelector('input[name="password"]');
  const confirmationInput = form.querySelector('input[name="password_confirmation"]');
  const submitBtn = form.querySelector(".btn-submit");

  const inputs = [emailInput, passwordInput, confirmationInput].filter(Boolean);

  inputs.forEach((input) => {
    input.addEventListener("input", () => {
      if (input.classList.contains("is-invalid")) {
        input.classList.remove("is-invalid");
        hideError(input);
      }

      if (input.name === "password_confirmation") {
        validateConfirmation(passwordInput, confirmationInput);
      }
    });

    input.addEventListener("blur", () => {
      if (input.name === "email") {
        input.value = input.value.trim().toLowerCase();
        validateEmail(input);
      }

      if (input.name === "password") {
        validatePassword(input);

        if (confirmationInput?.value) {
          validateConfirmation(passwordInput, confirmationInput);
        }
      }

      if (input.name === "password_confirmation") {
        validateConfirmation(passwordInput, confirmationInput);
      }
    });
  });

  form.addEventListener("submit", (event) => {
    if (emailInput) {
      emailInput.value = emailInput.value.trim().toLowerCase();
    }

    const emailValid = emailInput ? validateEmail(emailInput) : true;
    const passwordValid = passwordInput ? validatePassword(passwordInput) : true;
    const confirmationValid =
      passwordInput && confirmationInput
        ? validateConfirmation(passwordInput, confirmationInput)
        : true;

    if (!emailValid || !passwordValid || !confirmationValid) {
      event.preventDefault();

      const firstInvalid = form.querySelector(".form-input.is-invalid");

      if (firstInvalid) {
        firstInvalid.focus();
      }

      return;
    }

    if (submitBtn?.disabled) {
      event.preventDefault();
      return;
    }

    setSubmitLoading(submitBtn);
  });
}

function validateEmail(input) {
  const value = (input.value || "").trim();

  if (!value) {
    input.classList.add("is-invalid");
    showError(input, "Este campo é obrigatório.");
    return false;
  }

  if (!isValidEmail(value)) {
    input.classList.add("is-invalid");
    showError(input, "Introduz um email válido.");
    return false;
  }

  input.classList.remove("is-invalid");
  hideError(input);

  return true;
}

function validatePassword(input) {
  const value = input.value || "";

  if (!value.trim()) {
    input.classList.add("is-invalid");
    showError(input, "Este campo é obrigatório.");
    return false;
  }

  if (value.length < 8) {
    input.classList.add("is-invalid");
    showError(input, "A palavra-passe deve ter pelo menos 8 caracteres.");
    return false;
  }

  input.classList.remove("is-invalid");
  hideError(input);

  return true;
}

function validateConfirmation(passwordInput, confirmationInput) {
  if (!confirmationInput) {
    return true;
  }

  const password = passwordInput?.value || "";
  const confirmation = confirmationInput.value || "";

  if (!confirmation.trim()) {
    confirmationInput.classList.add("is-invalid");
    showError(confirmationInput, "Confirma a tua palavra-passe.");
    return false;
  }

  if (password !== confirmation) {
    confirmationInput.classList.add("is-invalid");
    showError(confirmationInput, "As palavras-passe não coincidem.");
    return false;
  }

  confirmationInput.classList.remove("is-invalid");
  hideError(confirmationInput);

  return true;
}

function showError(input, message) {
  const parent = input.closest(".form-group") || input.parentElement;

  if (!parent) {
    return;
  }

  let errorEl = parent.querySelector(".form-error");

  if (!errorEl) {
    errorEl = document.createElement("span");
    errorEl.className = "form-error";
    parent.appendChild(errorEl);
  }

  errorEl.innerHTML = `
    <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
    <span>${escapeHTML(message)}</span>
  `;

  errorEl.style.opacity = "1";
}

function hideError(input) {
  const parent = input.closest(".form-group") || input.parentElement;
  const errorEl = parent?.querySelector(".form-error");

  if (!errorEl) {
    return;
  }

  errorEl.style.opacity = "0";

  window.setTimeout(() => {
    errorEl.remove();
  }, 180);
}

function setSubmitLoading(submitBtn) {
  if (!submitBtn) {
    return;
  }

  submitBtn.disabled = true;
  submitBtn.classList.add("is-loading");

  submitBtn.innerHTML = `
    <span>A redefinir...</span>
    <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
  `;
}

function initAuthEntryAnimation() {
  const authCard = document.querySelector(".auth-card");

  authCard?.classList.add("is-entering");

  window.setTimeout(() => {
    authCard?.classList.add("is-visible");
  }, 80);
}

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function escapeHTML(value) {
  const div = document.createElement("div");
  div.textContent = String(value ?? "");

  return div.innerHTML;
}