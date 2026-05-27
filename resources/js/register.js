// ====================================
// REGISTER.JS — Wander Jar
// ====================================

document.addEventListener("DOMContentLoaded", () => {
  initPasswordToggles();
  initRegisterValidation();
  initCheckboxAnimation();
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

function initRegisterValidation() {
  const form = document.querySelector(".auth-form");

  if (!form) {
    return;
  }

  const inputs = Array.from(form.querySelectorAll(".form-input"));
  const submitBtn = form.querySelector(".btn-submit");
  const termsCheckbox = form.querySelector('input[name="terms"]');

  inputs.forEach((input) => {
    input.addEventListener("input", () => {
      if (input.classList.contains("is-invalid")) {
        input.classList.remove("is-invalid");
        hideError(input);
      }

      if (input.name === "password_confirmation") {
        validateInput(input, form);
      }
    });

    input.addEventListener("blur", () => {
      validateInput(input, form);
    });
  });

  if (termsCheckbox) {
    termsCheckbox.addEventListener("change", () => {
      validateTerms(termsCheckbox);
    });
  }

  const emailInput = form.querySelector('input[type="email"], input[name="email"]');

  if (emailInput) {
    emailInput.addEventListener("blur", () => {
      emailInput.value = emailInput.value.trim().toLowerCase();
    });
  }

  form.addEventListener("submit", (event) => {
    let isFormValid = true;

    inputs.forEach((input) => {
      if (!validateInput(input, form)) {
        isFormValid = false;
      }
    });

    if (termsCheckbox && !validateTerms(termsCheckbox)) {
      isFormValid = false;
    }

    if (!isFormValid) {
      event.preventDefault();

      const firstInvalid =
        form.querySelector(".form-input.is-invalid") ||
        form.querySelector(".form-error");

      firstInvalid?.scrollIntoView({
        behavior: "smooth",
        block: "center",
      });

      const firstInput = form.querySelector(".form-input.is-invalid");

      if (firstInput) {
        window.setTimeout(() => {
          firstInput.focus();
        }, 250);
      }

      return;
    }

    if (submitBtn?.disabled) {
      event.preventDefault();
      return;
    }

    setSubmitLoading(submitBtn);
  });

  const firstInput = form.querySelector(".form-input");

  if (firstInput && !firstInput.value) {
    window.setTimeout(() => {
      firstInput.focus();
    }, 300);
  }
}

function validateInput(input, form) {
  const value = (input.value || "").trim();
  const name = input.name;
  const type = input.type;

  let isValid = true;
  let errorMessage = "";

  if (input.required && value === "") {
    isValid = false;
    errorMessage = "Este campo é obrigatório.";
  }

  if (isValid && (type === "email" || name === "email") && value !== "") {
    if (!isValidEmail(value)) {
      isValid = false;
      errorMessage = "Introduz um email válido.";
    }
  }

  if (isValid && name === "name" && value.length > 0 && value.length < 2) {
    isValid = false;
    errorMessage = "O nome deve ter pelo menos 2 caracteres.";
  }

  if (isValid && name === "password" && value !== "" && value.length < 8) {
    isValid = false;
    errorMessage = "A palavra-passe deve ter pelo menos 8 caracteres.";
  }

  if (isValid && name === "password_confirmation") {
    const passwordInput = form.querySelector('input[name="password"]');

    if (value === "") {
      isValid = false;
      errorMessage = "Confirma a tua palavra-passe.";
    } else if (passwordInput && value !== passwordInput.value) {
      isValid = false;
      errorMessage = "As palavras-passe não coincidem.";
    }
  }

  if (!isValid) {
    input.classList.add("is-invalid");
    showError(input, errorMessage);
    return false;
  }

  input.classList.remove("is-invalid");
  hideError(input);

  return true;
}

function validateTerms(checkbox) {
  const parent = checkbox.closest(".form-terms");

  if (!parent) {
    return true;
  }

  let errorEl = parent.querySelector(".form-error");

  if (!checkbox.checked) {
    if (!errorEl) {
      errorEl = document.createElement("span");
      errorEl.className = "form-error";
      parent.appendChild(errorEl);
    }

    errorEl.innerHTML = `
      <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
      <span>Deves aceitar os termos para continuar.</span>
    `;

    return false;
  }

  if (errorEl) {
    errorEl.remove();
  }

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
    <span>A criar conta...</span>
    <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
  `;
}

function initCheckboxAnimation() {
  const checkboxes = document.querySelectorAll('.checkbox-label input[type="checkbox"]');

  checkboxes.forEach((checkbox) => {
    checkbox.addEventListener("change", () => {
      const customCheckbox = checkbox.nextElementSibling;

      if (!customCheckbox) {
        return;
      }

      customCheckbox.classList.add("is-bouncing");

      window.setTimeout(() => {
        customCheckbox.classList.remove("is-bouncing");
      }, 220);
    });
  });
}

function initAuthEntryAnimation() {
  const authCard = document.querySelector(".auth-card");
  const authSide = document.querySelector(".auth-side");

  authCard?.classList.add("is-entering");
  authSide?.classList.add("is-entering");

  window.setTimeout(() => {
    authCard?.classList.add("is-visible");
  }, 80);

  window.setTimeout(() => {
    authSide?.classList.add("is-visible");
  }, 160);
}

function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function escapeHTML(value) {
  const div = document.createElement("div");
  div.textContent = String(value ?? "");

  return div.innerHTML;
}