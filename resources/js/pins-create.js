document.addEventListener('DOMContentLoaded', () => {
  setupCustomSelects();
  setupImagePreview();
  setupPinSummary();
  setupPinMap();
});

/* =========================
   CUSTOM SELECT
========================= */
function setupCustomSelects() {
  const selects = document.querySelectorAll('[data-select]');

  if (!selects.length) {
    return;
  }

  const closeAll = (except = null) => {
    selects.forEach((select) => {
      if (select === except) {
        return;
      }

      select.classList.remove('is-open');

      const button = select.querySelector('[data-select-btn]');

      if (button) {
        button.setAttribute('aria-expanded', 'false');
      }
    });
  };

  selects.forEach((select) => {
    const key = select.dataset.select;
    const button = select.querySelector(`[data-select-btn="${key}"]`);
    const menu = select.querySelector(`[data-select-menu="${key}"]`);
    const valueLabel = select.querySelector(`[data-select-value="${key}"]`);
    const nativeSelect = document.getElementById('group_id');

    if (!button || !menu || !valueLabel || !nativeSelect) {
      return;
    }

    const options = Array.from(menu.querySelectorAll('.pins-create-select__opt'));

    const syncActive = () => {
      const currentValue = nativeSelect.value || '';

      options.forEach((option) => {
        const optionValue = option.dataset.value || '';
        const isActive = optionValue === currentValue;

        option.classList.toggle('is-active', isActive);
        option.setAttribute('aria-selected', String(isActive));
      });

      const activeOption = options.find((option) => {
        return (option.dataset.value || '') === currentValue;
      });

      if (activeOption) {
        valueLabel.textContent = activeOption.dataset.label || activeOption.textContent.trim();
      }
    };

    syncActive();

    button.addEventListener('click', (event) => {
      event.preventDefault();
      event.stopPropagation();

      const isOpen = select.classList.contains('is-open');

      closeAll(select);

      select.classList.toggle('is-open', !isOpen);
      button.setAttribute('aria-expanded', String(!isOpen));
    });

    options.forEach((option) => {
      option.addEventListener('click', (event) => {
        event.preventDefault();
        event.stopPropagation();

        const value = option.dataset.value || '';
        const label = option.dataset.label || option.textContent.trim();

        nativeSelect.value = value;
        valueLabel.textContent = label;

        syncActive();

        select.classList.remove('is-open');
        button.setAttribute('aria-expanded', 'false');

        nativeSelect.dispatchEvent(new Event('change', { bubbles: true }));
        button.focus();
      });
    });
  });

  document.addEventListener('click', (event) => {
    const clickedInside = event.target.closest('[data-select]');

    if (!clickedInside) {
      closeAll();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeAll();
    }
  });
}

/* =========================
   IMAGE PREVIEW
========================= */
function setupImagePreview() {
  const input = document.getElementById('imageInput');
  const preview = document.getElementById('imagePreview');
  const title = document.getElementById('uploadTitle');
  const removeImageCheckbox = document.querySelector('input[name="remove_image"]');

  if (!input || !preview || !title) {
    return;
  }

  const defaultTitle = title.textContent.trim() || 'Clica para escolher uma imagem';
  let objectUrl = null;

  const clearPreview = () => {
    if (objectUrl) {
      URL.revokeObjectURL(objectUrl);
      objectUrl = null;
    }

    preview.style.display = 'none';
    preview.removeAttribute('src');
    preview.alt = '';
    title.textContent = defaultTitle;
  };

  const showPreview = (file) => {
    if (objectUrl) {
      URL.revokeObjectURL(objectUrl);
      objectUrl = null;
    }

    objectUrl = URL.createObjectURL(file);

    preview.src = objectUrl;
    preview.alt = file.name;
    preview.style.display = 'block';
    title.textContent = file.name;
  };

  input.addEventListener('change', () => {
    const file = input.files?.[0];

    if (!file) {
      clearPreview();
      return;
    }

    const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

    if (!allowedTypes.includes(file.type)) {
      input.value = '';
      clearPreview();
      title.textContent = 'Formato inválido. Usa JPG, PNG ou WebP.';
      return;
    }

    const maxSize = 4 * 1024 * 1024;

    if (file.size > maxSize) {
      input.value = '';
      clearPreview();
      title.textContent = 'Imagem demasiado grande. Máximo 4 MB.';
      return;
    }

    if (removeImageCheckbox) {
      removeImageCheckbox.checked = false;
    }

    showPreview(file);
  });

  if (removeImageCheckbox) {
    removeImageCheckbox.addEventListener('change', () => {
      if (removeImageCheckbox.checked) {
        input.value = '';
        clearPreview();
        title.textContent = 'Imagem atual será removida';
        return;
      }

      title.textContent = defaultTitle;
    });
  }
}

/* =========================
   SUMMARY
========================= */
function setupPinSummary() {
  const titleInput = document.getElementById('pinTitle');
  const groupSelect = document.getElementById('group_id');
  const locationInput = document.getElementById('locationText');
  const latInput = document.getElementById('latInput');
  const lngInput = document.getElementById('lngInput');

  const summaryTitle = document.getElementById('summaryTitle');
  const summaryGroup = document.getElementById('summaryGroup');
  const summaryLocation = document.getElementById('summaryLocation');
  const summaryCoords = document.getElementById('summaryCoords');

  const updateSummary = () => {
    if (summaryTitle && titleInput) {
      summaryTitle.textContent = titleInput.value.trim() || 'Sem título';
    }

    if (summaryGroup && groupSelect) {
      const selectedOption = groupSelect.options[groupSelect.selectedIndex];
      summaryGroup.textContent = selectedOption?.textContent?.trim() || 'Pin pessoal';
    }

    if (summaryLocation && locationInput) {
      summaryLocation.textContent = locationInput.value.trim() || 'Sem localização';
    }

    if (summaryCoords && latInput && lngInput) {
      const lat = latInput.value.trim();
      const lng = lngInput.value.trim();

      summaryCoords.textContent = lat && lng
        ? `${Number.parseFloat(lat).toFixed(5)}, ${Number.parseFloat(lng).toFixed(5)}`
        : 'Por definir';
    }
  };

  [titleInput, groupSelect, locationInput, latInput, lngInput].forEach((element) => {
    if (!element) {
      return;
    }

    element.addEventListener('input', updateSummary);
    element.addEventListener('change', updateSummary);
  });

  updateSummary();
}

/* =========================
   MAP
========================= */
function setupPinMap() {
  const mapElement = document.getElementById('pinMap');

  if (!mapElement || typeof L === 'undefined') {
    return;
  }

  const latInput = document.getElementById('latInput');
  const lngInput = document.getElementById('lngInput');
  const locationTextInput = document.getElementById('locationText');
  const mapSearchButton = document.getElementById('mapSearchBtn');
  const useMyLocationButton = document.getElementById('useMyLocation');
  const clearLocationButton = document.getElementById('clearLocation');
  const mapError = document.getElementById('mapError');
  const pinForm = document.getElementById('pinForm');

  const defaultCenter = [39.5, -8.0];
  const defaultZoom = 7;

  const oldLat = latInput ? Number.parseFloat(latInput.value) : NaN;
  const oldLng = lngInput ? Number.parseFloat(lngInput.value) : NaN;

  const hasOldCoords = Number.isFinite(oldLat) && Number.isFinite(oldLng);

  const initialLat = hasOldCoords ? oldLat : defaultCenter[0];
  const initialLng = hasOldCoords ? oldLng : defaultCenter[1];
  const initialZoom = hasOldCoords ? 14 : defaultZoom;

  const map = L.map(mapElement, {
    zoomControl: true,
    scrollWheelZoom: true,
  }).setView([initialLat, initialLng], initialZoom);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap',
  }).addTo(map);

  let marker = null;

  const hideMapError = () => {
    if (!mapError) {
      return;
    }

    mapError.hidden = true;
    mapError.textContent = '';
  };

  const showMapError = (message = 'Escolhe um local no mapa antes de guardar.') => {
    if (!mapError) {
      return;
    }

    mapError.hidden = false;
    mapError.textContent = message;
  };

  const dispatchInputUpdates = () => {
    [latInput, lngInput, locationTextInput].forEach((input) => {
      if (!input) {
        return;
      }

      input.dispatchEvent(new Event('input', { bubbles: true }));
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });
  };

  const updateInputs = (lat, lng) => {
    if (latInput) {
      latInput.value = String(lat);
    }

    if (lngInput) {
      lngInput.value = String(lng);
    }

    hideMapError();
    dispatchInputUpdates();
  };

  const maybeFillLocationText = (address) => {
    if (!address || !locationTextInput) {
      return;
    }

    if (!locationTextInput.value.trim()) {
      locationTextInput.value = address;
      dispatchInputUpdates();
    }
  };

  const createOrMoveMarker = (lat, lng) => {
    if (!marker) {
      marker = L.marker([lat, lng], {
        draggable: true,
      }).addTo(map);

      marker.on('dragend', async () => {
        const position = marker.getLatLng();

        updateInputs(position.lat, position.lng);

        const address = await reverseGeocode(position.lat, position.lng);
        maybeFillLocationText(address);
      });

      return;
    }

    marker.setLatLng([lat, lng]);
  };

  const placeMarkerAndCenter = async (lat, lng, zoom = 14, updateAddress = true) => {
    createOrMoveMarker(lat, lng);
    updateInputs(lat, lng);
    map.setView([lat, lng], zoom);

    if (updateAddress) {
      const address = await reverseGeocode(lat, lng);
      maybeFillLocationText(address);
    }
  };

  const clearMarker = () => {
    if (marker) {
      map.removeLayer(marker);
      marker = null;
    }

    if (latInput) {
      latInput.value = '';
    }

    if (lngInput) {
      lngInput.value = '';
    }

    hideMapError();
    dispatchInputUpdates();
  };

  if (hasOldCoords) {
    createOrMoveMarker(initialLat, initialLng);
    updateInputs(initialLat, initialLng);
  }

  map.on('click', async (event) => {
    await placeMarkerAndCenter(
      event.latlng.lat,
      event.latlng.lng,
      Math.max(map.getZoom(), 14)
    );
  });

  if (mapSearchButton && locationTextInput) {
    const runSearch = async () => {
      const query = locationTextInput.value.trim();

      if (!query) {
        showMapError('Escreve uma localização para pesquisar.');
        return;
      }

      hideMapError();

      try {
        const result = await forwardGeocode(query);

        if (!result) {
          showMapError('Não foi possível encontrar esse local.');
          return;
        }

        if (locationTextInput) {
          locationTextInput.value = result.label || query;
        }

        await placeMarkerAndCenter(result.lat, result.lng, 14, false);
      } catch (error) {
        console.error(error);
        showMapError('Erro ao procurar localização. Tenta novamente.');
      }
    };

    mapSearchButton.addEventListener('click', runSearch);

    locationTextInput.addEventListener('keydown', (event) => {
      if (event.key === 'Enter') {
        event.preventDefault();
        runSearch();
      }
    });
  }

  if (useMyLocationButton) {
    useMyLocationButton.addEventListener('click', () => {
      if (!navigator.geolocation) {
        showMapError('O teu browser não suporta geolocalização.');
        return;
      }

      hideMapError();

      navigator.geolocation.getCurrentPosition(
        async (position) => {
          const lat = position.coords.latitude;
          const lng = position.coords.longitude;

          await placeMarkerAndCenter(lat, lng, 15);
        },
        () => {
          showMapError('Não foi possível obter a tua localização.');
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
        }
      );
    });
  }

  if (clearLocationButton) {
    clearLocationButton.addEventListener('click', () => {
      clearMarker();
    });
  }

  if (pinForm) {
    pinForm.addEventListener('submit', (event) => {
      const hasLat = latInput && latInput.value.trim() !== '';
      const hasLng = lngInput && lngInput.value.trim() !== '';

      if (!hasLat || !hasLng) {
        event.preventDefault();
        showMapError('Escolhe um local no mapa antes de guardar.');
        mapElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });
  }

  window.setTimeout(() => {
    map.invalidateSize();
  }, 200);
}

/* =========================
   GEOCODING HELPERS
========================= */
async function forwardGeocode(query) {
  const url = new URL('https://nominatim.openstreetmap.org/search');

  url.searchParams.set('q', query);
  url.searchParams.set('format', 'jsonv2');
  url.searchParams.set('limit', '1');

  const response = await fetch(url.toString(), {
    headers: {
      Accept: 'application/json',
    },
  });

  if (!response.ok) {
    return null;
  }

  const results = await response.json();

  if (!Array.isArray(results) || !results.length) {
    return null;
  }

  const item = results[0];

  return {
    lat: Number.parseFloat(item.lat),
    lng: Number.parseFloat(item.lon),
    label: item.display_name || query,
  };
}

async function reverseGeocode(lat, lng) {
  const url = new URL('https://nominatim.openstreetmap.org/reverse');

  url.searchParams.set('lat', String(lat));
  url.searchParams.set('lon', String(lng));
  url.searchParams.set('format', 'jsonv2');

  try {
    const response = await fetch(url.toString(), {
      headers: {
        Accept: 'application/json',
      },
    });

    if (!response.ok) {
      return null;
    }

    const data = await response.json();

    return data.display_name || null;
  } catch (error) {
    console.error(error);
    return null;
  }
}