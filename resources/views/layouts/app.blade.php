<!DOCTYPE html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>@yield('title', 'Wander Jar')</title>
  <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

  <link
    href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800;900&family=Playfair+Display:wght@400;600;700&display=swap"
    rel="stylesheet"
  >

  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
  >

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @stack('styles')
</head>

@php
  $isAdminArea = request()->routeIs('admin.*');
  $isHomePage = request()->routeIs('home') || request()->is('/');
@endphp

<body
  data-page="@yield('page-id', '')"
  class="{{ $isAdminArea ? 'is-admin-area' : '' }}"
>
  <header id="siteHeader" class="site-header {{ $isAdminArea ? 'site-header--admin' : '' }}">
    <div class="{{ $isAdminArea ? 'wj-container-xl header-inner' : 'container header-inner' }}">

      <div class="brand">
        <a href="{{ route('home') }}" class="brand-link" aria-label="Ir para a página inicial">
          <img src="{{ asset('img/logo.png') }}" alt="Wander Jar">
        </a>

        <div class="brand-text">
          <h1 class="brand-title">Wander Jar</h1>
          <p class="tagline">Onde as memórias ganham lugar</p>
        </div>
      </div>

      <nav class="nav-actions" aria-label="Navegação principal">
        @auth
          @php
            $u = Auth::user();
          @endphp

          @if ($u->is_admin && !request()->routeIs('admin.dashboard'))
            <a class="btn btn-secondary" href="{{ route('admin.dashboard') }}">
              <i class="bi bi-shield-lock" aria-hidden="true"></i>
              <span>Admin</span>
            </a>
          @endif

          @if (!request()->routeIs('dashboard'))
            <a class="btn btn-primary" href="{{ route('dashboard') }}">
              <i class="bi bi-house-door" aria-hidden="true"></i>
              <span>Início</span>
            </a>
          @endif

          <div class="user-chip" aria-label="Utilizador autenticado">
            <a href="{{ route('profile.edit') }}" class="user-chip__link" aria-label="Abrir perfil">
              @if($u->profile_photo)
                <img
                  src="{{ route('profile.photo.show', $u->id) }}?v={{ $u->updated_at->timestamp }}"
                  alt="Foto de perfil de {{ $u->name }}"
                  class="user-chip__photo"
                  onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                >

                <span class="user-chip__avatar" aria-hidden="true" style="display:none;">
                  {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                </span>
              @else
                <span class="user-chip__avatar" aria-hidden="true">
                  {{ mb_strtoupper(mb_substr($u->name, 0, 1)) }}
                </span>
              @endif

              <span class="user-chip__name">
                {{ $u->name }}

                @if($u->is_admin)
                  <span class="user-chip__role">Admin</span>
                @endif
              </span>
            </a>
          </div>

          <form method="POST" action="{{ route('logout') }}" class="nav-form">
            @csrf

            <button type="submit" class="nav-link nav-link-btn">
              <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
              <span>Sair</span>
            </button>
          </form>
        @else
          @if (!request()->routeIs('register'))
            <a class="btn btn-primary" href="{{ route('register') }}">
              <i class="bi bi-person-plus" aria-hidden="true"></i>
              <span>Criar conta</span>
            </a>
          @endif

          @if (!request()->routeIs('login'))
            <a class="btn btn-secondary" href="{{ route('login') }}">
              <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
              <span>Entrar</span>
            </a>
          @endif
        @endauth

        <button
          id="themeToggle"
          class="theme-toggle"
          aria-label="Mudar tema"
          type="button"
          data-theme-toggle="1"
        >
          <i class="bi bi-sun-fill icon-sun" aria-hidden="true"></i>
          <i class="bi bi-moon-stars-fill icon-moon" aria-hidden="true"></i>
        </button>
      </nav>

    </div>
  </header>

  @php
    $toast = null;

    if (session('success')) {
      $toast = ['type' => 'success', 'text' => session('success')];
    } elseif (session('error')) {
      $toast = ['type' => 'error', 'text' => session('error')];
    } elseif (session('info')) {
      $toast = ['type' => 'info', 'text' => session('info')];
    } elseif (session('status')) {
      $toast = ['type' => 'success', 'text' => session('status')];
    }
  @endphp

  @if ($toast)
    <div
      id="wjToast"
      class="wj-toast"
      data-type="{{ $toast['type'] }}"
      role="status"
      aria-live="polite"
      aria-atomic="true"
      data-autohide="1"
    >
      <div class="wj-toast__icon" aria-hidden="true">
        @if($toast['type'] === 'error')
          <i class="bi bi-x-circle-fill"></i>
        @elseif($toast['type'] === 'info')
          <i class="bi bi-info-circle-fill"></i>
        @else
          <i class="bi bi-check2-circle"></i>
        @endif
      </div>

      <div class="wj-toast__body">
        <div class="wj-toast__text">{{ $toast['text'] }}</div>
        <div class="wj-toast__sub">Esta mensagem desaparece automaticamente.</div>
      </div>

      <button type="button" class="wj-toast__close" aria-label="Fechar" data-toast-close="1">
        <i class="bi bi-x-lg" aria-hidden="true"></i>
      </button>
    </div>
  @endif

  <main class="page {{ $isAdminArea ? 'page--admin' : '' }}">
    @yield('content')
  </main>

  @if(!$isAdminArea && !$isHomePage)
    <footer id="siteFooter" class="site-footer">
      <div class="container footer-inner">
        <div>© {{ date('Y') }} Wander Jar</div>
        <div class="small">Projeto PAP — Gestão e Programação de Sistemas Informáticos</div>
      </div>
    </footer>
  @endif

  <div id="wjConfirm" class="wj-modal" aria-hidden="true">
    <div class="wj-modal__backdrop" data-confirm-cancel="1"></div>

    <div class="wj-modal__panel" role="dialog" aria-modal="true" aria-labelledby="wjConfirmTitle">
      <div class="wj-modal__head">
        <div class="wj-modal__icon" aria-hidden="true">
          <i class="bi bi-exclamation-triangle-fill"></i>
        </div>

        <div class="wj-modal__titles">
          <div id="wjConfirmTitle" class="wj-modal__title">Confirmar ação</div>
          <div class="wj-modal__subtitle" id="wjConfirmText">Tens a certeza?</div>
        </div>

        <button type="button" class="wj-modal__x" aria-label="Fechar" data-confirm-cancel="1">
          <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
      </div>

      <div class="wj-modal__actions">
        <button type="button" class="btn btn-ghost" data-confirm-cancel="1">
          Cancelar
        </button>

        <button type="button" class="btn btn-danger" data-confirm-ok="1">
          <i class="bi bi-trash3" aria-hidden="true"></i>
          <span>Apagar</span>
        </button>
      </div>
    </div>
  </div>

  @stack('scripts')
</body>
</html>