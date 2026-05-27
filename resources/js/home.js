document.documentElement.classList.add('js');

document.addEventListener('DOMContentLoaded', () => {
  const heroTitle = document.querySelector('.wj-h1');

  if (heroTitle && !heroTitle.dataset.lettersReady) {
    splitElementText(heroTitle);
    heroTitle.dataset.lettersReady = 'true';
  }

  document.querySelectorAll('.wj-titleChar').forEach((char, index) => {
    char.addEventListener('mouseenter', () => {
      const direction = index % 2 === 0 ? -1 : 1;
      const rotate = direction * (4 + Math.random() * 5);
      const lift = 7 + Math.random() * 6;

      char.style.setProperty('--char-rotate', `${rotate}deg`);
      char.style.setProperty('--char-lift', `-${lift}px`);
      char.classList.add('is-hovered');
    });

    char.addEventListener('mouseleave', () => {
      char.classList.remove('is-hovered');
    });
  });

  initReveal();
  initHeroFloat(heroTitle);
  initPressFeedback();
  initSmoothAnchors();
  initPreviewTilt();
  initProgressBar();
  initThemeSafety();
});

function createCharSpan(char, index) {
  const span = document.createElement('span');

  span.className = 'wj-titleChar';
  span.textContent = char;
  span.style.setProperty('--letter-index', index);

  return span;
}

function splitTextNode(textNode, startIndex) {
  const fragment = document.createDocumentFragment();
  const text = textNode.textContent || '';
  const parts = text.split(/(\s+)/);

  let index = startIndex;

  parts.forEach((part) => {
    if (!part) {
      return;
    }

    if (/^\s+$/.test(part)) {
      fragment.appendChild(document.createTextNode(part));
      return;
    }

    const word = document.createElement('span');

    word.className = 'wj-titleWord';

    [...part].forEach((char) => {
      word.appendChild(createCharSpan(char, index));
      index += 1;
    });

    fragment.appendChild(word);
  });

  return {
    fragment,
    nextIndex: index,
  };
}

function splitElementText(element, startIndex = 0) {
  const nodes = Array.from(element.childNodes);
  let index = startIndex;

  nodes.forEach((node) => {
    if (node.nodeType === Node.TEXT_NODE) {
      const result = splitTextNode(node, index);

      index = result.nextIndex;
      node.replaceWith(result.fragment);

      return;
    }

    if (node.nodeType === Node.ELEMENT_NODE && node.tagName !== 'BR') {
      index = splitElementText(node, index);
    }
  });

  return index;
}

function initReveal() {
  const revealItems = document.querySelectorAll('.reveal');

  if (!revealItems.length) {
    return;
  }

  if (!('IntersectionObserver' in window)) {
    revealItems.forEach((item) => {
      item.classList.add('show');
    });

    return;
  }

  const revealObserver = new IntersectionObserver(
    (entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        entry.target.classList.add('show');
        revealObserver.unobserve(entry.target);
      });
    },
    {
      threshold: 0.14,
      rootMargin: '0px 0px -45px 0px',
    }
  );

  revealItems.forEach((item) => {
    revealObserver.observe(item);
  });
}

function initHeroFloat(heroTitle) {
  const heroCopy = document.querySelector('.wj-heroCopy');
  const heroLead = document.querySelector('.wj-lead');
  const heroKicker = document.querySelector('.wj-kicker');

  if (!heroCopy || !heroTitle || !heroLead || !heroKicker) {
    return;
  }

  heroCopy.addEventListener('mousemove', (event) => {
    const rect = heroCopy.getBoundingClientRect();

    if (!rect.width || !rect.height) {
      return;
    }

    const x = (event.clientX - rect.left) / rect.width - 0.5;
    const y = (event.clientY - rect.top) / rect.height - 0.5;

    heroKicker.style.transform = `translate(${x * 6}px, ${y * 5}px)`;
    heroTitle.style.transform = `translate(${x * 9}px, ${y * 7}px)`;
    heroLead.style.transform = `translate(${x * 5}px, ${y * 4}px)`;
  });

  heroCopy.addEventListener('mouseleave', () => {
    heroKicker.style.transform = '';
    heroTitle.style.transform = '';
    heroLead.style.transform = '';
  });
}

function initPressFeedback() {
  const pressableItems = document.querySelectorAll(
    '.wj-btn, .wj-vibe, .wj-card, .wj-feature, .wj-step'
  );

  pressableItems.forEach((item) => {
    item.addEventListener('pointerdown', () => {
      item.classList.add('is-pressed');
    });

    const removePressed = () => {
      item.classList.remove('is-pressed');
    };

    item.addEventListener('pointerup', removePressed);
    item.addEventListener('pointercancel', removePressed);
    item.addEventListener('pointerleave', removePressed);
  });
}

function initSmoothAnchors() {
  document.querySelectorAll('a[href^="#"]').forEach((link) => {
    link.addEventListener('click', (event) => {
      const targetId = link.getAttribute('href');

      if (!targetId || targetId === '#') {
        return;
      }

      const target = document.querySelector(targetId);

      if (!target) {
        return;
      }

      event.preventDefault();

      target.scrollIntoView({
        behavior: 'smooth',
        block: 'start',
      });
    });
  });
}

function initPreviewTilt() {
  const tilt = document.querySelector('.wj-preview-tilt');
  const preview = document.querySelector('.wj-preview');

  if (!tilt) {
    return;
  }

  let frame = null;

  const clamp = (number, min, max) => Math.max(min, Math.min(max, number));

  const reset = () => {
    if (frame) {
      cancelAnimationFrame(frame);
    }

    frame = null;

    tilt.classList.remove('is-tilting');
    tilt.style.transform = '';

    if (preview) {
      preview.classList.remove('is-active');
    }
  };

  const handleMove = (event) => {
    const rect = tilt.getBoundingClientRect();

    if (!rect.width || !rect.height) {
      return;
    }

    const x = (event.clientX - rect.left) / rect.width - 0.5;
    const y = (event.clientY - rect.top) / rect.height - 0.5;

    const rotateY = clamp(x * 10, -6, 6);
    const rotateX = clamp(-y * 8, -5, 5);

    if (frame) {
      cancelAnimationFrame(frame);
    }

    frame = requestAnimationFrame(() => {
      tilt.classList.add('is-tilting');
      tilt.style.transform = `perspective(1200px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;

      if (preview) {
        preview.classList.add('is-active');
      }
    });
  };

  tilt.addEventListener('mousemove', handleMove);
  tilt.addEventListener('mouseleave', reset);
  window.addEventListener('scroll', reset, { passive: true });
}

function initProgressBar() {
  const progressBar = document.querySelector('.wj-bar');

  if (!progressBar) {
    return;
  }

  window.setTimeout(() => {
    progressBar.classList.add('is-loaded');
  }, 350);
}

function initThemeSafety() {
  const themeButtons = document.querySelectorAll(
    '[data-theme-toggle], #themeToggle, .theme-toggle, button[aria-label*="tema"], button[aria-label*="Tema"], button[title*="tema"], button[title*="Tema"]'
  );

  const tilt = document.querySelector('.wj-preview-tilt');
  const preview = document.querySelector('.wj-preview');
  const heroTitle = document.querySelector('.wj-h1');
  const heroLead = document.querySelector('.wj-lead');
  const heroKicker = document.querySelector('.wj-kicker');

  const cleanThemeSwitch = () => {
    document.documentElement.classList.add('theme-switching');

    if (tilt) {
      tilt.classList.remove('is-tilting');
      tilt.style.transform = '';
    }

    if (preview) {
      preview.classList.remove('is-active');
    }

    if (heroKicker) {
      heroKicker.style.transform = '';
    }

    if (heroTitle) {
      heroTitle.style.transform = '';
    }

    if (heroLead) {
      heroLead.style.transform = '';
    }

    window.setTimeout(() => {
      document.documentElement.classList.remove('theme-switching');
    }, 160);
  };

  themeButtons.forEach((button) => {
    button.addEventListener('click', cleanThemeSwitch);
  });
}