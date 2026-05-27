document.addEventListener("DOMContentLoaded", () => {
  initForgotPasswordValidation();
  initAuthEntryAnimation();
});

function initForgotPasswordValidation() {
  const form = document.querySelector(".auth-form");

  if (!form) {
    return;
  }

  const emailInput = form.querySelector('input[type="email"], input[name="email"]');
  const submitBtn = form.querySelector(".btn-submit");

  if (!emailInput) {
    return;
  }

  emailInput.addEventListener("input", () => {
    if (emailInput.classList.contains("is-invalid")) {
      emailInput.classList.remove("is-invalid");
      hideError(emailInput);
    }
  });

  emailInput.addEventListener("blur", () => {
    emailInput.value = emailInput.value.trim().toLowerCase();
    validateEmail(emailInput);
  });

  form.addEventListener("submit", (event) => {
    emailInput.value = emailInput.value.trim().toLowerCase();

    const isValid = validateEmail(emailInput);

    if (!isValid) {
      event.preventDefault();
      emailInput.focus();
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
    <span>A enviar...</span>
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