document.addEventListener('DOMContentLoaded', () => {
  const grid = document.getElementById('grGrid');
  const searchInput = document.getElementById('grSearch');
  const clearSearchBtn = document.getElementById('grClear');
  const filterButtons = document.querySelectorAll('[data-filter]');
  const layoutBtn = document.getElementById('grLayoutBtn');
  const sortBtn = document.getElementById('grSortBtn');
  const noResults = document.getElementById('grNoResults');

  const toast = document.getElementById('grToast');
  const toastText = document.getElementById('grToastText');

  let activeFilter = 'all';
  let sortDirection = 'desc';
  let isGridLayout = true;
  let toastTimer = null;

  if (window.__FLASH_STATUS__) {
    showToast(window.__FLASH_STATUS__);
  }

  setupInviteButtons();
  setupSearchAndFilters();
  setupLayoutButton();
  setupSortButton();

  function setupInviteButtons() {
    const inviteButtons = document.querySelectorAll('.gr-invite[data-invite]');

    inviteButtons.forEach((button) => {
      button.addEventListener('click', async () => {
        const inviteUrl = button.dataset.invite || '';
        const groupName = button.dataset.group || 'grupo';

        if (!inviteUrl) {
          showToast('Este grupo ainda não tem link de convite.');
          return;
        }

        const copied = await copyText(inviteUrl);

        if (copied) {
          showToast(`Link copiado: ${inviteUrl}`);
          return;
        }

        showInviteFallback(inviteUrl, groupName);
      });
    });
  }

  async function copyText(text) {
    try {
      if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(text);
        return true;
      }

      const input = document.createElement('input');

      input.value = text;
      input.setAttribute('readonly', '');
      input.style.position = 'fixed';
      input.style.top = '-9999px';
      input.style.left = '-9999px';
      input.style.opacity = '0';

      document.body.appendChild(input);
      input.focus();
      input.select();
      input.setSelectionRange(0, input.value.length);

      const success = document.execCommand('copy');

      input.remove();

      return success;
    } catch (error) {
      console.error(error);
      return false;
    }
  }

  function showInviteFallback(inviteUrl, groupName = 'grupo') {
    window.prompt(`Copia este link de convite para o ${groupName}:`, inviteUrl);
  }

  function setupSearchAndFilters() {
    if (!grid) {
      return;
    }

    if (searchInput) {
      searchInput.addEventListener('input', () => {
        if (clearSearchBtn) {
          clearSearchBtn.hidden = searchInput.value.trim() === '';
        }

        applyFilters();
      });
    }

    if (clearSearchBtn && searchInput) {
      clearSearchBtn.addEventListener('click', () => {
        searchInput.value = '';
        clearSearchBtn.hidden = true;
        searchInput.focus();
        applyFilters();
      });
    }

    filterButtons.forEach((button) => {
      button.addEventListener('click', () => {
        activeFilter = button.dataset.filter || 'all';

        filterButtons.forEach((item) => {
          item.classList.remove('is-active');
        });

        button.classList.add('is-active');
        applyFilters();
      });
    });

    applyFilters();
  }

  function applyFilters() {
    if (!grid) {
      return;
    }

    const cards = Array.from(grid.querySelectorAll('.gr-card'));
    const searchTerm = searchInput ? normalize(searchInput.value) : '';

    let visibleCount = 0;

    cards.forEach((card) => {
      const name = normalize(card.dataset.name || '');
      const role = card.dataset.role || 'member';

      const matchesSearch = !searchTerm || name.includes(searchTerm);
      const matchesFilter = activeFilter === 'all' || role === activeFilter;

      const shouldShow = matchesSearch && matchesFilter;

      card.hidden = !shouldShow;

      if (shouldShow) {
        visibleCount += 1;
      }
    });

    if (noResults) {
      noResults.hidden = visibleCount > 0;
    }
  }

  function setupLayoutButton() {
    if (!layoutBtn || !grid) {
      return;
    }

    grid.classList.add('is-grid');

    layoutBtn.addEventListener('click', () => {
      isGridLayout = !isGridLayout;

      grid.classList.toggle('is-grid', isGridLayout);
      grid.classList.toggle('is-list', !isGridLayout);

      const icon = layoutBtn.querySelector('i');

      if (icon) {
        icon.className = isGridLayout ? 'bi bi-grid-3x3-gap' : 'bi bi-view-list';
      }
    });
  }

  function setupSortButton() {
    if (!sortBtn || !grid) {
      return;
    }

    sortBtn.addEventListener('click', () => {
      sortDirection = sortDirection === 'desc' ? 'asc' : 'desc';

      const cards = Array.from(grid.querySelectorAll('.gr-card'));

      cards.sort((a, b) => {
        const aCreated = Number.parseInt(a.dataset.created || '0', 10);
        const bCreated = Number.parseInt(b.dataset.created || '0', 10);

        return sortDirection === 'desc'
          ? bCreated - aCreated
          : aCreated - bCreated;
      });

      cards.forEach((card) => {
        grid.appendChild(card);
      });

      const icon = sortBtn.querySelector('i');

      if (icon) {
        icon.className = sortDirection === 'desc' ? 'bi bi-sort-down' : 'bi bi-sort-up';
      }
    });
  }

  function showToast(message) {
    if (!toast || !toastText) {
      return;
    }

    toastText.textContent = message;
    toast.hidden = false;

    if (toastTimer) {
      clearTimeout(toastTimer);
    }

    toastTimer = setTimeout(() => {
      toast.hidden = true;
    }, 4200);
  }

  function normalize(value) {
    return String(value || '')
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '')
      .trim();
  }
});