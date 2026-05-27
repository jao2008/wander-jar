document.addEventListener('DOMContentLoaded', () => {
  const dateInput = document.getElementById('event_date');
  const timeInput = document.getElementById('event_time');
  const participantsInput = document.getElementById('max_participants');
  const locationInput = document.getElementById('location_text');

  const summaryDate = document.getElementById('summaryDate');
  const summaryTime = document.getElementById('summaryTime');
  const summaryParticipants = document.getElementById('summaryParticipants');
  const summaryLocation = document.getElementById('summaryLocation');

  const syncSummary = () => {
    if (summaryDate && dateInput) {
      summaryDate.textContent = dateInput.value || '—';
    }

    if (summaryTime && timeInput) {
      summaryTime.textContent = timeInput.value || '—';
    }

    if (summaryParticipants && participantsInput) {
      summaryParticipants.textContent = participantsInput.value || '—';
    }

    if (summaryLocation && locationInput) {
      summaryLocation.textContent = locationInput.value || '—';
    }
  };

  [dateInput, timeInput, participantsInput, locationInput].forEach((input) => {
    if (!input) {
      return;
    }

    input.addEventListener('input', syncSummary);
    input.addEventListener('change', syncSummary);
  });

  syncSummary();

  const mapEl = document.getElementById('eventMap');

  if (!mapEl || typeof L === 'undefined') {
    return;
  }

  const latInput = document.getElementById('lat');
  const lngInput = document.getElementById('lng');
  const coordsHint = document.getElementById('coordsHint');
  const searchInput = document.getElementById('mapSearch');
  const searchBtn = document.getElementById('mapSearchBtn');
  const locateBtn = document.getElementById('useMyLocation');
  const clearBtn = document.getElementById('clearLocation');

  const currentLat = Number(mapEl.dataset.lat);
  const currentLng = Number(mapEl.dataset.lng);
  const hasCoords = Number.isFinite(currentLat) && Number.isFinite(currentLng);

  const startLat = hasCoords ? currentLat : 39.7436;
  const startLng = hasCoords ? currentLng : -8.8071;
  const startZoom = hasCoords ? 14 : 7;

  const map = L.map(mapEl, {
    zoomControl: true,
    scrollWheelZoom: true,
  }).setView([startLat, startLng], startZoom);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; OpenStreetMap',
  }).addTo(map);

  let marker = null;

  const updateCoords = (lat, lng, zoom = true) => {
    const cleanLat = Number(lat).toFixed(7);
    const cleanLng = Number(lng).toFixed(7);

    if (latInput) {
      latInput.value = cleanLat;
    }

    if (lngInput) {
      lngInput.value = cleanLng;
    }

    if (coordsHint) {
      coordsHint.textContent = `${cleanLat}, ${cleanLng}`;
    }

    if (!marker) {
      marker = L.marker([lat, lng], {
        draggable: true,
      }).addTo(map);

      marker.on('dragend', () => {
        const position = marker.getLatLng();
        updateCoords(position.lat, position.lng, false);
      });
    } else {
      marker.setLatLng([lat, lng]);
    }

    if (zoom) {
      map.flyTo([lat, lng], 14, {
        duration: 0.55,
      });
    }
  };

  if (hasCoords) {
    updateCoords(currentLat, currentLng, false);
  }

  map.on('click', (event) => {
    updateCoords(event.latlng.lat, event.latlng.lng);
  });

  const searchLocation = async () => {
    const query = searchInput?.value?.trim();

    if (!query) {
      return;
    }

    try {
      const response = await fetch(
        `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&limit=1`,
        {
          headers: {
            Accept: 'application/json',
          },
        }
      );

      if (!response.ok) {
        return;
      }

      const results = await response.json();

      if (!Array.isArray(results) || results.length === 0) {
        return;
      }

      const result = results[0];
      const lat = Number(result.lat);
      const lng = Number(result.lon);

      if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
        return;
      }

      if (locationInput && !locationInput.value) {
        locationInput.value = result.display_name || query;
        locationInput.dispatchEvent(new Event('input'));
        locationInput.dispatchEvent(new Event('change'));
      }

      updateCoords(lat, lng);
    } catch (error) {
      console.warn('Não foi possível pesquisar a localização.', error);
    }
  };

  if (searchBtn) {
    searchBtn.addEventListener('click', searchLocation);
  }

  if (searchInput) {
    searchInput.addEventListener('keydown', (event) => {
      if (event.key === 'Enter') {
        event.preventDefault();
        searchLocation();
      }
    });
  }

  if (locateBtn && navigator.geolocation) {
    locateBtn.addEventListener('click', () => {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          updateCoords(position.coords.latitude, position.coords.longitude);
        },
        () => {
          if (coordsHint) {
            coordsHint.textContent = 'Não foi possível obter a tua localização.';
          }
        },
        {
          enableHighAccuracy: true,
          timeout: 10000,
          maximumAge: 0,
        }
      );
    });
  }

  if (clearBtn) {
    clearBtn.addEventListener('click', () => {
      if (latInput) {
        latInput.value = '';
      }

      if (lngInput) {
        lngInput.value = '';
      }

      if (coordsHint) {
        coordsHint.textContent = 'Clica no mapa para marcar a localização do evento.';
      }

      if (marker) {
        marker.remove();
        marker = null;
      }
    });
  }

  setTimeout(() => {
    map.invalidateSize();
  }, 250);

  window.addEventListener('resize', () => {
    map.invalidateSize();
  });
});