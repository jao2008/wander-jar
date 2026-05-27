// ====================================
// LOGIN.JS — Wander Jar
// ====================================

document.addEventListener("DOMContentLoaded", () => {
  initPasswordToggle();
  initLoginValidation();
  initRememberAnimation();
  initAuthEntryAnimation();
});

function initPasswordToggle() {
  const passwordToggles = document.querySelectorAll(".password-toggle");

  passwordToggles.forEach((toggle) => {
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

function initLoginValidation() {
  const form = document.querySelector(".auth-form");

  if (!form) {
    return;
  }

  const inputs = Array.from(form.querySelectorAll(".form-input"));
  const submitBtn = form.querySelector(".btn-submit");

  inputs.forEach((input) => {
    input.addEventListener("input", () => {
      if (input.classList.contains("is-invalid")) {
        input.classList.remove("is-invalid");
        hideError(input);
      }
    });

    input.addEventListener("blur", () => {
      validateInput(input);
    });
  });

  form.addEventListener("submit", (event) => {
    let isFormValid = true;

    inputs.forEach((input) => {
      if (!validateInput(input)) {
        isFormValid = false;
      }
    });

    if (!isFormValid) {
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

  const firstInput = form.querySelector(".form-input");

  if (firstInput && !firstInput.value) {
    window.setTimeout(() => {
      firstInput.focus();
    }, 300);
  }

  const emailInput = form.querySelector('input[type="email"], input[name="email"]');

  if (emailInput) {
    emailInput.addEventListener("blur", () => {
      emailInput.value = emailInput.value.trim().toLowerCase();
    });
  }
}

function validateInput(input) {
  const value = (input.value || "").trim();
  const type = input.type;
  const name = input.name;

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

  if (isValid && name === "password" && value !== "" && value.length < 8) {
    isValid = false;
    errorMessage = "A palavra-passe deve ter pelo menos 8 caracteres.";
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
    <span>A entrar...</span>
    <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
  `;
}

function initRememberAnimation() {
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