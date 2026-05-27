document.addEventListener("DOMContentLoaded", () => {
  initPasswordToggle();
  initConfirmPasswordValidation();
  initAuthEntryAnimation();
});

function initPasswordToggle() {
  const toggle = document.querySelector(".password-toggle");

  if (!toggle) {
    return;
  }

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
}

function initConfirmPasswordValidation() {
  const form = document.querySelector(".auth-form");

  if (!form) {
    return;
  }

  const passwordInput = form.querySelector('input[name="password"]');
  const submitBtn = form.querySelector(".btn-submit");

  if (!passwordInput) {
    return;
  }

  passwordInput.addEventListener("input", () => {
    if (passwordInput.classList.contains("is-invalid")) {
      passwordInput.classList.remove("is-invalid");
      hideError(passwordInput);
    }
  });

  passwordInput.addEventListener("blur", () => {
    validatePassword(passwordInput);
  });

  form.addEventListener("submit", (event) => {
    const isValid = validatePassword(passwordInput);

    if (!isValid) {
      event.preventDefault();
      passwordInput.focus();
      return;
    }

    if (submitBtn?.disabled) {
      event.preventDefault();
      return;
    }

    setSubmitLoading(submitBtn);
  });
}

function validatePassword(input) {
  const value = input.value || "";

  if (!value.trim()) {
    input.classList.add("is-invalid");
    showError(input, "Este campo é obrigatório.");
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
    <span>A confirmar...</span>
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

function escapeHTML(value) {
  const div = document.createElement("div");
  div.textContent = String(value ?? "");

  return div.innerHTML;
}