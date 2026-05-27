document.addEventListener("DOMContentLoaded", () => {
  const el = document.getElementById("eventMap");
  if (!el || typeof L === "undefined") return;

  const lat = parseFloat(el.dataset.lat);
  const lng = parseFloat(el.dataset.lng);
  const title = el.dataset.title || "Evento";

  if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;

  const map = L.map("eventMap", {
    scrollWheelZoom: true,
  }).setView([lat, lng], 12);

  L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
    attribution: "&copy; OpenStreetMap contributors",
  }).addTo(map);

  L.marker([lat, lng]).addTo(map).bindPopup(title);
});
