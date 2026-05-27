/* ===============================
   EVENTS INDEX JS
   - Leaflet map init
   - Event markers from cards
   - Toggle show/hide map + invalidateSize
   - Optional custom select support
   =============================== */

let eventsMapInstance = null;

function escapeHtml(value) {
  return String(value ?? "")
    .replaceAll("&", "&amp;")
    .replaceAll("<", "&lt;")
    .replaceAll(">", "&gt;")
    .replaceAll('"', "&quot;")
    .replaceAll("'", "&#039;");
}

function initEventsMap() {
  const el = document.getElementById("eventsMap");

  if (!el || typeof L === "undefined") {
    return null;
  }

  const map = L.map(el, {
    zoomControl: true,
    attributionControl: true,
  }).setView([39.5, -8.0], 6);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    maxZoom: 19,
    attribution: "&copy; OpenStreetMap",
  }).addTo(map);

  const cards = document.querySelectorAll("[data-event-card]");
  const bounds = [];

  cards.forEach((card) => {
    const lat = Number.parseFloat(card.dataset.lat);
    const lng = Number.parseFloat(card.dataset.lng);

    if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
      return;
    }

    const title = card.dataset.title || "Evento";
    const location = card.dataset.location || "";
    const date = card.dataset.date || "";
    const time = card.dataset.time || "";
    const url = card.dataset.url || card.getAttribute("href") || "#";

    const popupHtml = `
      <div class="events-popup">
        <div class="events-popup__title">
          ${escapeHtml(title)}
        </div>

        ${
          location
            ? `<div class="events-popup__line">📍 ${escapeHtml(location)}</div>`
            : ""
        }

        ${
          date || time
            ? `<div class="events-popup__line">
                ${date ? `📅 ${escapeHtml(date)}` : ""}
                ${date && time ? " · " : ""}
                ${time ? `⏰ ${escapeHtml(time)}` : ""}
              </div>`
            : ""
        }

        <a class="events-popup__link" href="${escapeHtml(url)}">
          Ver detalhes ↗
        </a>
      </div>
    `;

    const marker = L.marker([lat, lng]).addTo(map);

    marker.bindPopup(popupHtml, {
      maxWidth: 280,
      className: "events-popup-wrap",
    });

    marker.on("click", () => {
      cards.forEach((item) => item.classList.remove("is-active"));
      card.classList.add("is-active");
    });

    card.addEventListener("mouseenter", () => {
      marker.openPopup();
    });

    card.addEventListener("focus", () => {
      marker.openPopup();
    });

    bounds.push([lat, lng]);
  });

  if (bounds.length === 1) {
    map.setView(bounds[0], 13);
  } else if (bounds.length > 1) {
    map.fitBounds(bounds, {
      padding: [36, 36],
      maxZoom: 13,
    });
  }

  setTimeout(() => {
    map.invalidateSize();
  }, 150);

  return map;
}

function setupMapToggle() {
  const toggle = document.querySelector("[data-map-toggle]");
  const mapWrap = document.querySelector("[data-map-wrap]");

  if (!toggle || !mapWrap) {
    return;
  }

  toggle.addEventListener("click", () => {
    const hidden = mapWrap.classList.toggle("is-hidden");

    toggle.setAttribute("aria-expanded", String(!hidden));

    toggle.innerHTML = hidden
      ? `<i class="bi bi-eye"></i><span>Mostrar</span>`
      : `<i class="bi bi-eye-slash"></i><span>Ocultar</span>`;

    if (!hidden && eventsMapInstance) {
      setTimeout(() => {
        eventsMapInstance.invalidateSize();
      }, 150);
    }
  });
}

function setupCustomSelects() {
  document.querySelectorAll("[data-select]").forEach((select) => {
    const btn = select.querySelector("[data-select-btn]");
    const hidden = select.querySelector('input[type="hidden"]');
    const valueLabel = select.querySelector(".wj-select__value");
    const opts = select.querySelectorAll(".wj-select__opt");

    if (!btn || !hidden || !valueLabel || !opts.length) {
      return;
    }

    const open = () => {
      select.classList.add("is-open");
      btn.setAttribute("aria-expanded", "true");
    };

    const close = () => {
      select.classList.remove("is-open");
      btn.setAttribute("aria-expanded", "false");
    };

    btn.addEventListener("click", (event) => {
      event.preventDefault();
      event.stopPropagation();

      if (select.classList.contains("is-open")) {
        close();
      } else {
        open();
      }
    });

    opts.forEach((opt) => {
      opt.addEventListener("click", (event) => {
        event.preventDefault();
        event.stopPropagation();

        const val = opt.dataset.value ?? "";

        hidden.value = val;
        valueLabel.textContent = opt.textContent.trim();

        opts.forEach((item) => item.classList.remove("is-active"));
        opt.classList.add("is-active");

        close();
      });
    });

    document.addEventListener("click", (event) => {
      if (!select.contains(event.target)) {
        close();
      }
    });

    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape") {
        close();
      }
    });
  });
}

document.addEventListener("DOMContentLoaded", () => {
  setupCustomSelects();
  setupMapToggle();

  eventsMapInstance = initEventsMap();

  if (eventsMapInstance) {
    setTimeout(() => {
      eventsMapInstance.invalidateSize();
    }, 200);
  }
});