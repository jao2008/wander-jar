document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("gcForm");

  const nameInput = document.getElementById("groupName");
  const nameCounter = document.getElementById("nameCounter");
  const errName = document.getElementById("errName");

  const sumName = document.getElementById("sumName");

  const modal = document.getElementById("gcModal");
  const inviteInput = document.getElementById("gcInviteInput");
  const copyInvite = document.getElementById("gcCopyInvite");

  const toast = document.querySelector(".gc-toast");
  const toastText = document.getElementById("gcToastText");
  let toastTimer;

  const btnGenerate = document.getElementById("gcGenerate");
  const btnInvite = document.getElementById("gcInvite");

  function showToast(msg) {
    if (!toast || !toastText) return;

    toastText.textContent = msg;
    toast.hidden = false;

    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => {
      toast.hidden = true;
    }, 2200);
  }

  function openModal() {
    if (!modal) return;
    modal.setAttribute("aria-hidden", "false");
  }

  function closeModal() {
    if (!modal) return;
    modal.setAttribute("aria-hidden", "true");
  }

  modal?.addEventListener("click", (e) => {
    const target = e.target;

    if (
      target &&
      target.getAttribute &&
      target.getAttribute("data-close") === "1"
    ) {
      closeModal();
    }
  });

  document.addEventListener("keydown", (e) => {
    if (
      e.key === "Escape" &&
      modal?.getAttribute("aria-hidden") === "false"
    ) {
      closeModal();
    }
  });

  copyInvite?.addEventListener("click", async () => {
    try {
      await navigator.clipboard.writeText(inviteInput?.value || "");
      showToast("Copiado ✅");
    } catch {
      showToast("Não deu para copiar");
    }
  });

  function updateCounter() {
    if (!nameInput || !nameCounter) return;

    const len = (nameInput.value || "").length;
    nameCounter.textContent = `${len}/40`;
  }

  function updateSummary() {
    if (!sumName) return;

    sumName.textContent = (nameInput?.value || "").trim() || "—";
  }

  form?.addEventListener("submit", (e) => {
    const value = (nameInput?.value || "").trim();
    const valid = value.length >= 3;

    if (!valid) {
      e.preventDefault();

      if (errName) {
        errName.hidden = false;
      }

      nameInput?.focus();
      showToast("Corrige o nome do grupo");
      return;
    }

    if (errName) {
      errName.hidden = true;
    }
  });

  nameInput?.addEventListener("input", () => {
    if (errName) {
      errName.hidden = true;
    }

    updateCounter();
    updateSummary();
  });

  btnInvite?.addEventListener("click", () => {
    openModal();
  });

  btnGenerate?.addEventListener("click", () => {
    const samples = [
      "Miradouros",
      "Viagem de Verão",
      "Praias",
      "Trilhos",
      "Food Spots",
      "Memórias"
    ];

    const pick = samples[Math.floor(Math.random() * samples.length)];

    if (nameInput) {
      nameInput.value = pick;
    }

    updateCounter();
    updateSummary();
    showToast("Nome gerado");
  });

  updateCounter();
  updateSummary();
});