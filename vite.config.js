import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
  plugins: [
    laravel({
      input: [
        // =========================
        // GLOBAL
        // =========================
        'resources/css/app.css',
        'resources/js/app.js',

        // =========================
        // MAPA PESSOAL
        // =========================
        'resources/css/map.css',
        'resources/js/map.js',

        // =========================
        // HOME
        // =========================
        'resources/css/home.css',
        'resources/js/home.js',

        // =========================
        // DASHBOARD
        // =========================
        'resources/css/dashboard.css',
        'resources/js/dashboard.js',

        // =========================
        // GRUPOS
        // =========================
        'resources/css/groups.css',
        'resources/js/groups.js',

        'resources/css/groups-create.css',
        'resources/js/groups-create.js',

        'resources/css/group-map.css',
        'resources/js/group-map.js',

        'resources/css/group-chat.css',
        'resources/js/group-chat.js',

        // =========================
        // PINS
        // =========================
        'resources/css/pins-index.css',
        'resources/js/pins-index.js',

        'resources/css/pins-create.css',
        'resources/js/pins-create.js',

        // =========================
        // EVENTS
        // =========================
        'resources/css/events-index.css',
        'resources/js/events-index.js',

        'resources/css/events-create.css',
        'resources/js/events-create.js',

        'resources/css/events-show.css',
        'resources/js/events-show.js',

        // =========================
        // AUTH
        // =========================
        'resources/css/login.css',
        'resources/js/login.js',

        'resources/css/register.css',
        'resources/js/register.js',

        'resources/css/forgot-password.css',
        'resources/js/forgot-password.js',

        'resources/css/reset-password.css',
        'resources/js/reset-password.js',

        'resources/css/confirm-password.css',
        'resources/js/confirm-password.js',

        'resources/css/verify-email.css',
        'resources/js/verify-email.js',
      ],
      refresh: true,
    }),
  ],
});