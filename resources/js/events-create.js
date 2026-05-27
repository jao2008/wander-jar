// resources/js/events-create.js

document.addEventListener("DOMContentLoaded", () => {
  const mapEl = document.getElementById("eventMap");
  if (!mapEl) return;

  const latInput = document.getElementById("lat");
  const lngInput = document.getElementById("lng");
  const locationInput = document.getElementById("location_text");
  const mapError = document.getElementById("mapError");

  // Default Portugal
  const DEFAULT = { lat: 39.5, lng: -8.0, zoom: 6 };

  const oldLat = parseFloat(latInput?.value || "");
  const oldLng = parseFloat(lngInput?.value || "");

  const startLat = Number.isFinite(oldLat) ? oldLat : DEFAULT.lat;
  const startLng = Number.isFinite(oldLng) ? oldLng : DEFAULT.lng;
  const startZoom = Number.isFinite(oldLat) && Number.isFinite(oldLng) ? 13 : DEFAULT.zoom;

  const map = L.map(mapEl, {
    zoomControl: true,
    scrollWheelZoom: true,
  }).setView([startLat, startLng], startZoom);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map);

  let marker = null;

  function setMarker(lat, lng) {
    if (marker) marker.remove();
    marker = L.marker([lat, lng]).addTo(map);
  }

  async function reverseGeocode(lat, lng) {
    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${encodeURIComponent(
      lat
    )}&lon=${encodeURIComponent(lng)}`;

    const res = await fetch(url, {
      headers: {
        "Accept": "application/json",
      },
    });

    if (!res.ok) throw new Error("reverse_geocode_failed");

    const data = await res.json();
    return data?.display_name || "";
  }

  // Se já tem coords (old), mete marker
  if (Number.isFinite(oldLat) && Number.isFinite(oldLng)) {
    setMarker(oldLat, oldLng);
  }

  map.on("click", async (e) => {
    const lat = +e.latlng.lat.toFixed(7);
    const lng = +e.latlng.lng.toFixed(7);

    if (latInput) latInput.value = String(lat);
    if (lngInput) lngInput.value = String(lng);

    setMarker(lat, lng);

    if (locationInput) {
      const current = (locationInput.value || "").trim();

      try {
        if (mapError) mapError.hidden = true;

        const name = await reverseGeocode(lat, lng);
        if (name) {
          if (!current) locationInput.value = name;
        }
      } catch (err) {
        if (mapError) {
          mapError.hidden = false;
          mapError.textContent =
            "Não foi possível preencher o local automaticamente. Podes escrever manualmente.";
        }
      }
    }
  });

  setTimeout(() => map.invalidateSize(), 200);
});