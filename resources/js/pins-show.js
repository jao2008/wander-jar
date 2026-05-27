document.addEventListener('DOMContentLoaded', () => {
  initPinShowMap();
  initPressFeedback();
  initImageFallback();
});

function initPinShowMap() {
  const mapEl = document.getElementById('pinShowMap');

  if (!mapEl) {
    return;
  }

  if (typeof window.L === 'undefined') {
    mapEl.innerHTML = `
      <div class="pins-show-map-error">
        <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
        <strong>Não foi possível carregar o mapa.</strong>
        <span>Atualiza a página e tenta novamente.</span>
      </div>
    `;
    return;
  }

  const lat = Number(mapEl.dataset.lat);
  const lng = Number(mapEl.dataset.lng);
  const title = mapEl.dataset.title || 'Pin';
  const location = mapEl.dataset.location || 'Localização do pin';

  if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
    mapEl.innerHTML = `
      <div class="pins-show-map-error">
        <i class="bi bi-geo-alt" aria-hidden="true"></i>
        <strong>Coordenadas indisponíveis.</strong>
        <span>Este pin não tem uma localização válida no mapa.</span>
      </div>
    `;
    return;
  }

  const L = window.L;

  const map = L.map(mapEl, {
    zoomControl: true,
    scrollWheelZoom: false,
    dragging: true,
    doubleClickZoom: true,
    touchZoom: true,
    boxZoom: false,
    keyboard: true,
    tap: true,
    zoomAnimation: true,
    fadeAnimation: true,
    markerZoomAnimation: true,
  }).setView([lat, lng], 14);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap',
  }).addTo(map);

  const pinIcon = L.divIcon({
    className: '',
    html: '<div class="pin-show-marker"></div>',
    iconSize: [18, 18],
    iconAnchor: [9, 9],
    popupAnchor: [0, -14],
  });

  const marker = L.marker([lat, lng], {
    icon: pinIcon,
    riseOnHover: true,
  }).addTo(map);

  marker.bindPopup(
    `
      <article class="pin-show-popup">
        <strong>${escapeHTML(title)}</strong>
        <span>${escapeHTML(location)}</span>
      </article>
    `,
    {
      closeButton: true,
      maxWidth: 260,
      className: 'pin-show-popup-wrap',
    }
  );

  window.setTimeout(() => {
    map.invalidateSize(true);
    marker.openPopup();
  }, 280);

  window.addEventListener(
    'resize',
    debounce(() => {
      map.invalidateSize(true);
    }, 160),
    { passive: true }
  );
}

function initPressFeedback() {
  const buttons = document.querySelectorAll('.pins-show-btn');

  buttons.forEach((button) => {
    button.addEventListener('pointerdown', () => {
      button.classList.add('is-pressed');
    });

    const clear = () => {
      button.classList.remove('is-pressed');
    };

    button.addEventListener('pointerup', clear);
    button.addEventListener('pointercancel', clear);
    button.addEventListener('pointerleave', clear);
  });
}

function initImageFallback() {
  const image = document.getElementById('pinsShowImage');
  const fallback = document.getElementById('pinsShowImageFallback');

  if (!image || !fallback) {
    return;
  }

  image.addEventListener('error', () => {
    image.style.display = 'none';
    fallback.hidden = false;
    fallback.style.display = 'flex';
  });
}

function escapeHTML(value) {
  const div = document.createElement('div');
  div.textContent = String(value ?? '');

  return div.innerHTML;
}

function debounce(callback, wait = 150) {
  let timeout = null;

  return (...args) => {
    window.clearTimeout(timeout);

    timeout = window.setTimeout(() => {
      callback(...args);
    }, wait);
  };
}