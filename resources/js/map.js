document.addEventListener('DOMContentLoaded', () => {
  const mapEl = document.getElementById('personalMap');

  if (!mapEl) {
    return;
  }

  if (typeof window.L === 'undefined') {
    showError(mapEl, 'Não foi possível carregar o mapa. Atualiza a página e tenta novamente.');
    return;
  }

  const L = window.L;

  const pins = safeParseJSON(document.getElementById('pinsData')?.textContent) ?? [];
  const pinsWithCoords = pins.filter((pin) => isValidCoord(pin.lat) && isValidCoord(pin.lng));

  const locateBtn = document.getElementById('locateBtn');
  const pinsListEl = document.getElementById('pinsList');
  const pinsCountEl = document.getElementById('pinsCount');
  const mapHint = document.getElementById('mapHint');

  if (pinsCountEl) {
    animateCounter(pinsCountEl, 0, pinsWithCoords.length, 700);
  }

  if (mapHint) {
    mapHint.style.display = pinsWithCoords.length > 0 ? 'none' : 'flex';
  }

  const defaultCenter = [39.5, -8.0];
  const defaultZoom = 6;

  const map = L.map(mapEl, {
    zoomControl: true,
    scrollWheelZoom: true,
    dragging: true,
    doubleClickZoom: true,
    touchZoom: true,
    boxZoom: true,
    keyboard: true,
    tap: true,
    zoomAnimation: true,
    fadeAnimation: true,
    markerZoomAnimation: true,
  }).setView(defaultCenter, defaultZoom);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap',
    className: 'map-tiles',
  }).addTo(map);

  const pinIcon = L.divIcon({
    className: '',
    html: '<div class="wj-pin"></div>',
    iconSize: [20, 20],
    iconAnchor: [10, 10],
    popupAnchor: [0, -15],
  });

  const userIcon = L.divIcon({
    className: '',
    html: '<div class="wj-user-pin"></div>',
    iconSize: [22, 22],
    iconAnchor: [11, 11],
    popupAnchor: [0, -16],
  });

  const markerById = new Map();
  const markers = [];
  let userMarker = null;

  pinsWithCoords.forEach((pin, index) => {
    const lat = Number(pin.lat);
    const lng = Number(pin.lng);

    const marker = L.marker([lat, lng], {
      icon: pinIcon,
      riseOnHover: true,
    });

    setTimeout(() => {
      marker.addTo(map);
    }, index * 45);

    marker.bindPopup(buildPopupHTML(pin), {
      closeButton: true,
      autoPan: true,
      autoPanPadding: [50, 50],
      maxWidth: 360,
      minWidth: 300,
      className: 'custom-popup',
    });

    marker.on('popupopen', () => {
      setActiveListItem(String(pin.id));
      addPulseEffect(marker);
    });

    marker.on('popupclose', () => {
      removePulseEffect(marker);
    });

    markers.push(marker);
    markerById.set(String(pin.id), marker);
  });

  if (markers.length > 0) {
    setTimeout(() => {
      fitPins(true);
    }, 300);
  }

  if (locateBtn) {
    locateBtn.addEventListener('click', (event) => {
      event.preventDefault();
      goToMyLocation(true);
    });
  }

  if (pinsListEl) {
    pinsListEl.addEventListener('click', (event) => {
      const button = event.target.closest('.map-list__btn');

      if (!button) {
        return;
      }

      const item = button.closest('.map-list__item');
      const pinId = item?.getAttribute('data-pin-id');

      if (!pinId) {
        return;
      }

      const marker = markerById.get(String(pinId));

      if (!marker) {
        return;
      }

      const lat = Number(button.dataset.lat);
      const lng = Number(button.dataset.lng);

      if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
        return;
      }

      const targetZoom = Math.max(map.getZoom(), 13);

      map.flyTo([lat, lng], targetZoom, {
        animate: true,
        duration: 0.8,
        easeLinearity: 0.25,
      });

      setTimeout(() => {
        marker.openPopup();
      }, 400);

      setActiveListItem(String(pinId));
      pressButton(button);
    });
  }

  requestAnimationFrame(() => {
    map.invalidateSize(true);
  });

  setTimeout(() => {
    map.invalidateSize(true);
  }, 300);

  let resizeTimeout;

  window.addEventListener(
    'resize',
    () => {
      clearTimeout(resizeTimeout);

      resizeTimeout = setTimeout(() => {
        map.invalidateSize(true);
      }, 150);
    },
    { passive: true }
  );

  if (typeof ResizeObserver !== 'undefined') {
    const observer = new ResizeObserver(() => {
      map.invalidateSize(true);
    });

    observer.observe(mapEl);
  }

  function fitPins(animate = true) {
    if (!markers.length) {
      showNotification('Ainda não existem pins para enquadrar.', 'info');
      return;
    }

    const bounds = L.featureGroup(markers).getBounds();

    if (!bounds.isValid()) {
      return;
    }

    map.fitBounds(bounds, {
      padding: [42, 42],
      animate,
      duration: 0.8,
      easeLinearity: 0.25,
      maxZoom: 15,
    });
  }

  function goToMyLocation(animate = true) {
    if (!('geolocation' in navigator)) {
      showNotification('O teu navegador não suporta geolocalização.', 'info');
      return;
    }

    setLocateLoading(true);

    navigator.geolocation.getCurrentPosition(
      (position) => {
        const { latitude, longitude } = position.coords;
        const targetZoom = Math.max(map.getZoom(), 16);

        map.flyTo([latitude, longitude], targetZoom, {
          animate,
          duration: 0.9,
          easeLinearity: 0.25,
        });

        if (userMarker) {
          userMarker.setLatLng([latitude, longitude]);
        } else {
          userMarker = L.marker([latitude, longitude], {
            icon: userIcon,
            riseOnHover: true,
          }).addTo(map);

          userMarker.bindPopup('Estás aqui');
        }

        setTimeout(() => {
          userMarker?.openPopup();
        }, 500);

        showNotification('Localização encontrada.', 'success');
        setLocateLoading(false);
      },
      (error) => {
        setLocateLoading(false);

        if (error?.code === 1) {
          showNotification('Permissão de localização negada.', 'info');
          return;
        }

        if (error?.code === 2) {
          showNotification('Não foi possível obter a tua localização.', 'info');
          return;
        }

        if (error?.code === 3) {
          showNotification('A localização demorou demasiado tempo.', 'info');
          return;
        }

        showNotification('Não foi possível obter a tua localização.', 'info');
      },
      {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 15000,
      }
    );
  }

  function setLocateLoading(isLoading) {
    if (!locateBtn) {
      return;
    }

    locateBtn.disabled = isLoading;
    locateBtn.classList.toggle('is-loading', isLoading);
  }

  function setActiveListItem(pinId) {
    document.querySelectorAll('.map-list__btn.is-active').forEach((element) => {
      element.classList.remove('is-active');
    });

    const target = document.querySelector(
      `.map-list__item[data-pin-id="${cssEscape(pinId)}"] .map-list__btn`
    );

    if (!target) {
      return;
    }

    target.classList.add('is-active');

    const scrollContainer = target.closest('.map-side__scroll');

    if (!scrollContainer) {
      return;
    }

    const targetRect = target.getBoundingClientRect();
    const containerRect = scrollContainer.getBoundingClientRect();

    if (targetRect.top < containerRect.top || targetRect.bottom > containerRect.bottom) {
      target.scrollIntoView({
        behavior: 'smooth',
        block: 'nearest',
      });
    }
  }

  function addPulseEffect(marker) {
    const icon = marker.getElement();
    const pin = icon?.querySelector('.wj-pin');

    if (pin) {
      pin.classList.add('is-pulsing');
    }
  }

  function removePulseEffect(marker) {
    const icon = marker.getElement();
    const pin = icon?.querySelector('.wj-pin');

    if (pin) {
      pin.classList.remove('is-pulsing');
    }
  }

  function buildPopupHTML(pin) {
    const title = escapeHTML(pin.title || 'Sem título');
    const description = escapeHTML(pin.content || 'Sem descrição.');
    const location = escapeHTML(pin.location_text || 'Localização não definida');
    const url = pin.url ? escapeAttr(pin.url) : '#';

    const image = pin.image_url
      ? `
        <div class="wj-popup__img">
          <img
            src="${escapeAttr(pin.image_url)}"
            alt="Imagem do pin ${escapeAttr(pin.title || 'sem título')}"
            loading="lazy"
          >
        </div>
      `
      : '';

    return `
      <article class="wj-popup">
        ${image}

        <div class="wj-popup__body">
          <h3 class="wj-popup__title">${title}</h3>

          <p class="wj-popup__desc">${description}</p>

          <div class="wj-popup__row">
            <span class="wj-popup__chip">
              <span aria-hidden="true">📍</span>
              ${location}
            </span>
          </div>

          <a class="wj-popup__link" href="${url}">
            Ver detalhes
          </a>
        </div>
      </article>
    `;
  }

  function pressButton(button) {
    button.classList.add('is-pressed');

    setTimeout(() => {
      button.classList.remove('is-pressed');
    }, 150);
  }
});

function animateCounter(element, start, end, duration) {
  const range = end - start;
  const startTime = performance.now();

  function update(currentTime) {
    const elapsed = currentTime - startTime;
    const progress = Math.min(elapsed / duration, 1);
    const eased = 1 - Math.pow(1 - progress, 3);
    const current = Math.floor(start + range * eased);

    element.textContent = current;

    if (progress < 1) {
      requestAnimationFrame(update);
      return;
    }

    element.textContent = end;
  }

  requestAnimationFrame(update);
}

function showNotification(message, type = 'info') {
  const toast = document.createElement('div');

  toast.className = `map-toast map-toast--${type}`;
  toast.textContent = message;

  document.body.appendChild(toast);

  requestAnimationFrame(() => {
    toast.classList.add('is-visible');
  });

  setTimeout(() => {
    toast.classList.remove('is-visible');

    setTimeout(() => {
      toast.remove();
    }, 250);
  }, 2600);
}

function showError(container, message) {
  container.innerHTML = `
    <div class="map-error">
      <div class="map-error__icon" aria-hidden="true">
        <i class="bi bi-exclamation-triangle"></i>
      </div>

      <strong>Erro ao carregar</strong>

      <p>${escapeHTML(message)}</p>
    </div>
  `;
}

function safeParseJSON(value) {
  try {
    return JSON.parse(value || '[]');
  } catch {
    return null;
  }
}

function isValidCoord(value) {
  const number = Number(value);

  return Number.isFinite(number) && number !== 0;
}

function escapeHTML(value) {
  const div = document.createElement('div');
  div.textContent = String(value ?? '');

  return div.innerHTML;
}

function escapeAttr(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;');
}

function cssEscape(value) {
  if (window.CSS?.escape) {
    return window.CSS.escape(String(value));
  }

  return String(value).replace(/"/g, '\\"');
}