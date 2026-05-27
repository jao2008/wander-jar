document.addEventListener("DOMContentLoaded", () => {
  initVerifyEmailActions();
  initAuthEntryAnimation();
});

function initVerifyEmailActions() {
  const resendForm = document.querySelector(".verify-form");
  const logoutForm = document.querySelector(".logout-form");

  if (resendForm) {
    resendForm.addEventListener("submit", () => {
      const submitBtn = resendForm.querySelector(".btn-submit");

      if (!submitBtn || submitBtn.disabled) {
        return;
      }

      submitBtn.disabled = true;
      submitBtn.classList.add("is-loading");

      submitBtn.innerHTML = `
        <span>A reenviar...</span>
        <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
      `;
    });
  }

  if (logoutForm) {
    logoutForm.addEventListener("submit", () => {
      const logoutBtn = logoutForm.querySelector(".btn-logout");

      if (!logoutBtn || logoutBtn.disabled) {
        return;
      }

      logoutBtn.disabled = true;

      logoutBtn.innerHTML = `
        <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
        <span>A sair...</span>
      `;
    });
  }
}

function initAuthEntryAnimation() {
  const authCard = document.querySelector(".auth-card");

  authCard?.classList.add("is-entering");

  window.setTimeout(() => {
    authCard?.classList.add("is-visible");
  }, 80);
}