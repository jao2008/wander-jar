import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

/**
 * ==========================================
 * REALTIME (Echo + Reverb) — ONLY HERE
 * ==========================================
 */
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

const csrf =
  document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

window.Echo = new Echo({
  broadcaster: "reverb",
  key: import.meta.env.VITE_REVERB_APP_KEY,

  wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
  wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
  wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),

  forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? "http") === "https",
  enabledTransports: ["ws", "wss"],

  // ✅ necessário para private/presence auth
  authEndpoint: "/broadcasting/auth",
  auth: {
    headers: {
      "X-CSRF-TOKEN": csrf,
      "X-Requested-With": "XMLHttpRequest",
    },
  },

  disableStats: true,
});

console.log("✅ Echo pronto (bootstrap.js)");