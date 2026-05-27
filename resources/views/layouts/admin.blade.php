<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page-title', 'Admin') — Wander Jar</title>
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

    @vite([
        'resources/css/app.css',
        'resources/css/admin-dashboard.css',
        'resources/js/app.js'
    ])

    @stack('head')
</head>

<body class="admin-body is-admin-area">

<div class="admin-shell" id="adminShell">
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand-link">
                <div class="sidebar-brand-icon">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                        <path
                            d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"
                            fill="currentColor"
                            opacity="0.92"
                        />
                        <circle
                            cx="12"
                            cy="9"
                            r="2.5"
                            fill="white"
                            opacity="0.88"
                        />
                    </svg>
                </div>

                <div class="sidebar-brand-text">
                    <span class="brand-name">Wander Jar</span>
                    <span class="brand-badge">Admin</span>
                </div>
            </a>
        </div>

        <div class="sidebar-section-label">
            Gestão
        </div>

        <nav class="sidebar-nav" aria-label="Navegação da área administrativa">
            <a
                href="{{ route('admin.dashboard') }}"
                class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"
                title="Resumo"
            >
                <span class="sidebar-item-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="3" width="7" height="7" rx="1.5"/>
                        <rect x="3" y="14" width="7" height="7" rx="1.5"/>
                        <rect x="14" y="14" width="7" height="7" rx="1.5"/>
                    </svg>
                </span>

                <span class="sidebar-item-label">Resumo</span>

                @if(request()->routeIs('admin.dashboard'))
                    <span class="sidebar-item-dot"></span>
                @endif
            </a>

            <a
                href="{{ route('admin.users') }}"
                class="sidebar-item {{ request()->routeIs('admin.users') ? 'active' : '' }}"
                title="Utilizadores"
            >
                <span class="sidebar-item-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </span>

                <span class="sidebar-item-label">Utilizadores</span>

                @if(request()->routeIs('admin.users'))
                    <span class="sidebar-item-dot"></span>
                @endif
            </a>

            <a
                href="{{ route('admin.groups') }}"
                class="sidebar-item {{ request()->routeIs('admin.groups') ? 'active' : '' }}"
                title="Grupos"
            >
                <span class="sidebar-item-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M3 21v-2a4 4 0 0 1 4-4h4a4 4 0 0 1 4 4v2"/>
                        <circle cx="17" cy="7" r="3"/>
                        <path d="M21 21v-2a3 3 0 0 0-2-2.83"/>
                    </svg>
                </span>

                <span class="sidebar-item-label">Grupos</span>

                @if(request()->routeIs('admin.groups'))
                    <span class="sidebar-item-dot"></span>
                @endif
            </a>

            <a
                href="{{ route('admin.pins') }}"
                class="sidebar-item {{ request()->routeIs('admin.pins') ? 'active' : '' }}"
                title="Pins"
            >
                <span class="sidebar-item-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                        <circle cx="12" cy="9" r="2.5"/>
                    </svg>
                </span>

                <span class="sidebar-item-label">Pins</span>

                @if(request()->routeIs('admin.pins'))
                    <span class="sidebar-item-dot"></span>
                @endif
            </a>

            <a
                href="{{ route('admin.events') }}"
                class="sidebar-item {{ request()->routeIs('admin.events') ? 'active' : '' }}"
                title="Eventos"
            >
                <span class="sidebar-item-icon">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </span>

                <span class="sidebar-item-label">Eventos</span>

                @if(request()->routeIs('admin.events'))
                    <span class="sidebar-item-dot"></span>
                @endif
            </a>
        </nav>

        <div class="sidebar-spacer"></div>

        <div class="sidebar-footer">
            <a
                href="{{ route('home') }}"
                class="sidebar-back-link"
                title="Voltar ao site"
            >
                <span class="sidebar-item-icon">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                </span>

                <span class="sidebar-item-label">Voltar ao site</span>
            </a>

            <button
                type="button"
                class="sidebar-toggle-btn"
                id="adminSidebarToggle"
                aria-label="Encolher ou expandir menu lateral"
                aria-expanded="true"
                title="Encolher menu"
            >
                <svg
                    width="13"
                    height="13"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    stroke-width="2.2"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
            </button>
        </div>
    </aside>

    <div class="admin-main" id="adminMain">
        <header class="admin-topbar">
            <div class="topbar-left">
                <div class="topbar-breadcrumb">
                    <span class="topbar-breadcrumb-root">Admin</span>

                    <svg width="12" height="12" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor"
                         stroke-width="2.5"
                         stroke-linecap="round"
                         stroke-linejoin="round"
                         style="opacity:.25">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>

                    <span class="topbar-breadcrumb-current">
                        @yield('page-title', 'Resumo')
                    </span>
                </div>
            </div>

            <div class="topbar-right">
                <div class="topbar-status">
                    <span class="status-dot"></span>
                    <span class="status-label">Área administrativa</span>
                </div>

                <div class="topbar-user">
                    <div class="topbar-avatar">
                        {{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}
                    </div>

                    <div class="topbar-user-info">
                        <span class="topbar-user-name">
                            {{ auth()->user()->name ?? 'Admin' }}
                        </span>

                        <span class="topbar-user-role">
                            Administrador
                        </span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                    @csrf

                    <button
                        type="submit"
                        class="topbar-logout"
                        title="Terminar sessão"
                    >
                        <svg width="15" height="15" viewBox="0 0 24 24"
                             fill="none"
                             stroke="currentColor"
                             stroke-width="2"
                             stroke-linecap="round"
                             stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </button>
                </form>
            </div>
        </header>

        @if(session('success') || session('error') || session('info'))
            @php
                $flashType = session('success')
                    ? 'success'
                    : (session('error') ? 'error' : 'info');

                $flashMsg = session('success')
                    ?? session('error')
                    ?? session('info');
            @endphp

            <div
                class="admin-flash admin-flash--{{ $flashType }}"
                data-admin-flash
            >
                <span>{{ $flashMsg }}</span>

                <button
                    type="button"
                    class="admin-flash-close"
                    data-admin-flash-close
                    aria-label="Fechar mensagem"
                >
                    <svg width="13" height="13" viewBox="0 0 24 24"
                         fill="none"
                         stroke="currentColor"
                         stroke-width="2.5">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>
        @endif

        <main class="admin-content">
            @yield('content')
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('adminSidebar');
    const main = document.getElementById('adminMain');
    const toggle = document.getElementById('adminSidebarToggle');

    if (sidebar && main && toggle) {
        const savedState = localStorage.getItem('wj_admin_sidebar_state');

        const setSidebarState = (collapsed) => {
            sidebar.classList.toggle('collapsed', collapsed);
            main.classList.toggle('sidebar-collapsed', collapsed);

            toggle.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
            toggle.setAttribute('title', collapsed ? 'Expandir menu' : 'Encolher menu');

            localStorage.setItem(
                'wj_admin_sidebar_state',
                collapsed ? 'collapsed' : 'expanded'
            );
        };

        setSidebarState(savedState === 'collapsed');

        toggle.addEventListener('click', () => {
            const isCollapsed = sidebar.classList.contains('collapsed');
            setSidebarState(!isCollapsed);
        });
    }

    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', (e) => {
            if (!confirm(form.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-admin-flash]').forEach((flash) => {
        const close = flash.querySelector('[data-admin-flash-close]');

        const hideFlash = () => {
            flash.style.opacity = '0';
            flash.style.transform = 'translateY(-6px)';

            setTimeout(() => {
                flash.remove();
            }, 220);
        };

        if (close) {
            close.addEventListener('click', hideFlash);
        }

        setTimeout(hideFlash, 4500);
    });
});
</script>

@stack('scripts')

</body>
</html>