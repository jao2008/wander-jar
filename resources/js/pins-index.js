document.addEventListener('DOMContentLoaded', () => {
  initPinsSelects();
  initPinsCards();
});

function initPinsSelects() {
  const selects = Array.from(document.querySelectorAll('[data-wj-select]'));

  if (!selects.length) {
    return;
  }

  const closeAllSelects = (except = null) => {
    selects.forEach((select) => {
      if (except && select === except) {
        return;
      }

      closeSelect(select);
    });
  };

  selects.forEach((select) => {
    const button = select.querySelector('[data-wj-select-btn]');
    const hiddenInput = select.querySelector('[data-wj-select-value]');
    const label = select.querySelector('[data-wj-select-label]');
    const menu = select.querySelector('[data-wj-select-menu]');
    const options = Array.from(select.querySelectorAll('.pins-select__opt'));

    if (!button || !hiddenInput || !label || !menu || !options.length) {
      return;
    }

    const syncActiveOption = () => {
      const currentValue = hiddenInput.value || '';

      options.forEach((option) => {
        const optionValue = option.dataset.value || '';
        const isActive = optionValue === currentValue;

        option.classList.toggle('is-active', isActive);
        option.setAttribute('aria-selected', String(isActive));
        option.tabIndex = isActive ? 0 : -1;
      });

      const activeOption = options.find((option) => {
        return (option.dataset.value || '') === currentValue;
      });

      label.textContent = activeOption
        ? activeOption.dataset.label || activeOption.textContent.trim()
        : 'Todos';
    };

    syncActiveOption();

    button.addEventListener('click', (event) => {
      event.preventDefault();

      const willOpen = !select.classList.contains('is-open');

      closeAllSelects(select);

      if (willOpen) {
        openSelect(select);

        const activeOption =
          options.find((option) => option.classList.contains('is-active')) ||
          options[0];

        window.setTimeout(() => {
          activeOption?.focus();
        }, 40);
      } else {
        closeSelect(select);
      }
    });

    button.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowDown' || event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();

        closeAllSelects(select);
        openSelect(select);

        const activeOption =
          options.find((option) => option.classList.contains('is-active')) ||
          options[0];

        window.setTimeout(() => {
          activeOption?.focus();
        }, 40);
      }
    });

    options.forEach((option, index) => {
      option.addEventListener('click', () => {
        chooseOption(select, option);
      });

      option.addEventListener('keydown', (event) => {
        if (event.key === 'Enter' || event.key === ' ') {
          event.preventDefault();
          chooseOption(select, option);
          return;
        }

        if (event.key === 'ArrowDown') {
          event.preventDefault();

          const next = options[index + 1] || options[0];
          next.focus();
          return;
        }

        if (event.key === 'ArrowUp') {
          event.preventDefault();

          const previous = options[index - 1] || options[options.length - 1];
          previous.focus();
          return;
        }

        if (event.key === 'Escape') {
          event.preventDefault();

          closeSelect(select);
          button.focus();
        }
      });
    });

    function chooseOption(currentSelect, option) {
      const value = option.dataset.value || '';
      const text = option.dataset.label || option.textContent.trim();

      hiddenInput.value = value;
      label.textContent = text;

      syncActiveOption();
      closeSelect(currentSelect);
      button.focus();
    }
  });

  document.addEventListener('click', (event) => {
    const clickedInsideSelect = event.target.closest('[data-wj-select]');

    if (!clickedInsideSelect) {
      closeAllSelects();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeAllSelects();
    }
  });
}

function openSelect(select) {
  const button = select.querySelector('[data-wj-select-btn]');

  select.classList.add('is-open', 'is-active');

  if (button) {
    button.setAttribute('aria-expanded', 'true');
  }
}

function closeSelect(select) {
  const button = select.querySelector('[data-wj-select-btn]');

  select.classList.remove('is-open', 'is-active');

  if (button) {
    button.setAttribute('aria-expanded', 'false');
  }
}

function initPinsCards() {
  const cards = document.querySelectorAll('.pins-card, .pins-btn');

  cards.forEach((card) => {
    card.addEventListener('pointerdown', () => {
      card.classList.add('is-pressed');
    });

    const clear = () => {
      card.classList.remove('is-pressed');
    };

    card.addEventListener('pointerup', clear);
    card.addEventListener('pointercancel', clear);
    card.addEventListener('pointerleave', clear);
  });
}