// resources/js/app.js

/*
|--------------------------------------------------------------------------
| Bootstrap (Echo + Reverb) — isolado
|--------------------------------------------------------------------------
| Importado de forma dinâmica e protegida: se o Echo/Reverb falhar (ex:
| variáveis de ambiente em falta, servidor Reverb fora do ar), o resto
| do app.js continua a correr na mesma — incluindo o tema.
*/

import("./bootstrap").catch((error) => {
  console.error("Falha ao carregar bootstrap/echo:", error);
});

/*
|--------------------------------------------------------------------------
| Aplicar tema imediatamente
|--------------------------------------------------------------------------
| Isto corre antes do DOM estar pronto para evitar flash errado.
*/

(function () {
  const savedTheme = localStorage.getItem("theme");
  const systemPrefersDark =
    window.matchMedia &&
    window.matchMedia("(prefers-color-scheme: dark)").matches;

  if (savedTheme === "dark" || (!savedTheme && systemPrefersDark)) {
    document.documentElement.classList.add("dark-mode");
  } else {
    document.documentElement.classList.remove("dark-mode");
  }
})();

document.addEventListener("DOMContentLoaded", () => {
  const html = document.documentElement;

  /*
  |--------------------------------------------------------------------------
  | Theme toggle
  |--------------------------------------------------------------------------
  */

  const themeToggle = document.getElementById("themeToggle");

  const resetPageAnimationsAfterThemeChange = () => {
    html.classList.add("theme-switching");

    document.querySelectorAll(".is-tilting").forEach((el) => {
      el.classList.remove("is-tilting");
      el.style.transform = "";
    });

    document.querySelectorAll(".is-active").forEach((el) => {
      if (
        el.classList.contains("wj-preview") ||
        el.classList.contains("wj-card") ||
        el.classList.contains("wj-vibe")
      ) {
        el.classList.remove("is-active");
      }
    });

    document.querySelectorAll(
      ".wj-h1, .wj-kicker, .wj-lead, .wj-preview-tilt"
    ).forEach((el) => {
      el.style.transform = "";
    });

    window.dispatchEvent(
      new CustomEvent("wanderjar:theme-changed", {
        detail: {
          theme: html.classList.contains("dark-mode") ? "dark" : "light",
        },
      })
    );

    window.setTimeout(() => {
      html.classList.remove("theme-switching");
    }, 180);
  };

  if (themeToggle) {
    themeToggle.addEventListener("click", () => {
      html.classList.toggle("dark-mode");

      const theme = html.classList.contains("dark-mode") ? "dark" : "light";
      localStorage.setItem("theme", theme);

      resetPageAnimationsAfterThemeChange();
    });

    const darkModeQuery = window.matchMedia("(prefers-color-scheme: dark)");

    darkModeQuery?.addEventListener?.("change", (event) => {
      if (!localStorage.getItem("theme")) {
        html.classList.toggle("dark-mode", !!event.matches);
        resetPageAnimationsAfterThemeChange();
      }
    });
  } else {
    console.warn("Theme toggle: botão #themeToggle não encontrado no DOM.");
  }

  /*
  |--------------------------------------------------------------------------
  | Header height + hide/show
  |--------------------------------------------------------------------------
  */

  const header = document.getElementById("siteHeader");

  if (header) {
    const setHeaderHeight = () => {
      const height = header.offsetHeight || 92;

      html.style.setProperty("--header-h", `${height}px`);
      header.dataset.height = String(height);
    };

    setHeaderHeight();

    let lastScrollY = window.scrollY;
    let currentY = 0;
    let targetY = 0;
    let ticking = false;
    let isHidden = false;

    const DEADZONE = 6;
    const HIDE_OFFSET = 80;

    let headerHeight = header.offsetHeight || 92;

    const HIDE_EASE = 0.12;
    const SHOW_EASE = 0.22;

    const prefersReducedMotion =
      window.matchMedia &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    const updateTarget = (scrollY) => {
      const delta = scrollY - lastScrollY;

      if (Math.abs(delta) < DEADZONE) {
        return;
      }

      const scrollingDown = delta > 0;

      if (scrollingDown) {
        if (scrollY > headerHeight + HIDE_OFFSET && !isHidden) {
          targetY = -headerHeight;
          isHidden = true;
          header.setAttribute("aria-hidden", "true");
        }
      } else if (isHidden) {
        targetY = 0;
        isHidden = false;
        header.setAttribute("aria-hidden", "false");
      }

      lastScrollY = scrollY;
    };

    const animateHeader = () => {
      ticking = false;

      if (prefersReducedMotion) {
        currentY = targetY;
      } else {
        const ease = targetY < currentY ? HIDE_EASE : SHOW_EASE;
        const distance = targetY - currentY;

        if (Math.abs(distance) < 0.5) {
          currentY = targetY;
        } else {
          currentY += distance * ease;
        }
      }

      header.style.transform = `translateY(${currentY}px)`;

      if (isHidden) {
        header.classList.add("is-hidden");
      } else {
        header.classList.remove("is-hidden");
      }

      if (!prefersReducedMotion && Math.abs(currentY - targetY) > 0.5) {
        ticking = true;
        requestAnimationFrame(animateHeader);
      }
    };

    const onScroll = () => {
      const scrollY = window.scrollY || document.documentElement.scrollTop || 0;

      if (scrollY < 10) {
        targetY = 0;
        isHidden = false;
        header.setAttribute("aria-hidden", "false");
      } else {
        updateTarget(scrollY);
      }

      if (!ticking) {
        ticking = true;
        requestAnimationFrame(animateHeader);
      }
    };

    window.addEventListener("scroll", onScroll, { passive: true });

    let resizeTimeout;

    window.addEventListener(
      "resize",
      () => {
        clearTimeout(resizeTimeout);

        resizeTimeout = window.setTimeout(() => {
          setHeaderHeight();
          headerHeight = header.offsetHeight || 92;

          targetY = 0;
          currentY = 0;
          isHidden = false;

          header.style.transform = "translateY(0px)";
          header.classList.remove("is-hidden");
          header.setAttribute("aria-hidden", "false");
        }, 150);
      },
      { passive: true }
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Toast auto-hide
  |--------------------------------------------------------------------------
  */

  const toast = document.getElementById("wjToast");

  if (toast && toast.dataset.autohide === "1") {
    const showToast = () => {
      toast.classList.add("is-show");
    };

    const hideToast = () => {
      toast.classList.remove("is-show");
      toast.classList.add("is-hide");

      window.setTimeout(() => {
        toast.remove();
      }, 400);
    };

    window.setTimeout(showToast, 100);
    window.setTimeout(hideToast, 4500);

    const closeBtn = toast.querySelector('[data-toast-close="1"]');

    if (closeBtn) {
      closeBtn.addEventListener("click", hideToast);
    }
  }

  /*
  |--------------------------------------------------------------------------
  | Confirm modal
  |--------------------------------------------------------------------------
  */

  const modal = document.getElementById("wjConfirm");
  const txt = document.getElementById("wjConfirmText");
  const cancelBtns = modal?.querySelectorAll('[data-confirm-cancel="1"]');
  const okBtn = modal?.querySelector('[data-confirm-ok="1"]');

  let pendingForm = null;

  const openConfirm = (message, formEl) => {
    if (!modal) {
      return;
    }

    pendingForm = formEl;

    if (txt) {
      txt.textContent = message || "Tens a certeza?";
    }

    modal.classList.add("is-open");
    modal.setAttribute("aria-hidden", "false");
    document.body.classList.add("wj-modal-open");

    const focusable = modal.querySelector(
      'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
    );

    focusable?.focus?.();
  };

  const closeConfirm = () => {
    if (!modal) {
      return;
    }

    pendingForm = null;

    modal.classList.remove("is-open");
    modal.setAttribute("aria-hidden", "true");
    document.body.classList.remove("wj-modal-open");
  };

  document.querySelectorAll("form[data-confirm='1']").forEach((form) => {
    form.addEventListener("submit", (event) => {
      event.preventDefault();

      openConfirm(
        form.getAttribute("data-confirm-text") || "Tens a certeza?",
        form
      );
    });
  });

  cancelBtns?.forEach((btn) => {
    btn.addEventListener("click", closeConfirm);
  });

  okBtn?.addEventListener("click", () => {
    if (pendingForm) {
      pendingForm.submit();
    }

    closeConfirm();
  });

  modal?.addEventListener("click", (event) => {
    const clickedBackdrop = event.target?.classList?.contains("wj-modal__backdrop");

    if (clickedBackdrop) {
      closeConfirm();
    }
  });

  window.addEventListener("keydown", (event) => {
    if (event.key === "Escape" && modal?.classList.contains("is-open")) {
      closeConfirm();
    }
  });
});