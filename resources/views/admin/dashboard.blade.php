@extends('layouts.admin')

@section('page-title', 'Resumo')

@section('content')

<div class="admin-hero">
    <div class="admin-hero-content">
        <div class="admin-hero-eyebrow">
            <span class="hero-eyebrow-dot"></span>
            Painel de Administração
        </div>

        <h1 class="admin-hero-title">Visão Geral</h1>

        <p class="admin-hero-desc">
            Consulta os principais dados da plataforma Wander Jar e acompanha a atividade geral do sistema.
        </p>
    </div>

    <div class="admin-hero-meta">
        <div class="hero-date">
            {{ now()->locale('pt')->translatedFormat('l, j \d\e F \d\e Y') }}
        </div>
    </div>
</div>

<section class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Métricas Principais</h2>
        <span class="section-badge">Resumo</span>
    </div>

    <div class="metrics-grid">

        <div class="metric-card metric-card--accent">
            <div class="metric-card-icon">
                <i class="bi bi-people"></i>
            </div>

            <div class="metric-card-body">
                <span class="metric-label">Utilizadores</span>

                <span class="metric-value">{{ $stats['users'] }}</span>

                <span class="metric-sub">
                    {{ $quickStats['new_users_7d'] > 0 ? '+' . $quickStats['new_users_7d'] . ' esta semana' : 'Total registados' }}
                </span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-card-icon">
                <i class="bi bi-collection"></i>
            </div>

            <div class="metric-card-body">
                <span class="metric-label">Grupos</span>

                <span class="metric-value">{{ $stats['groups'] }}</span>

                <span class="metric-sub">
                    {{ $quickStats['new_groups_7d'] > 0 ? '+' . $quickStats['new_groups_7d'] . ' esta semana' : 'Grupos criados' }}
                </span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-card-icon">
                <i class="bi bi-geo-alt"></i>
            </div>

            <div class="metric-card-body">
                <span class="metric-label">Pins</span>

                <span class="metric-value">{{ $stats['pins'] }}</span>

                <span class="metric-sub">
                    {{ $quickStats['new_pins_7d'] > 0 ? '+' . $quickStats['new_pins_7d'] . ' esta semana' : 'Total de pins' }}
                </span>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-card-icon">
                <i class="bi bi-calendar-event"></i>
            </div>

            <div class="metric-card-body">
                <span class="metric-label">Eventos</span>

                <span class="metric-value">{{ $stats['events'] }}</span>

                <span class="metric-sub">
                    {{ ($quickStats['new_events_7d'] ?? 0) > 0 ? '+' . $quickStats['new_events_7d'] . ' esta semana' : 'Eventos criados' }}
                </span>
            </div>
        </div>

    </div>
</section>

<section class="admin-section">
    <div class="section-header">
        <h2 class="section-title">Detalhe de Pins</h2>
    </div>

    <div class="breakdown-grid">

        <div class="breakdown-card">
            <div class="breakdown-card-label">
                <span class="breakdown-dot breakdown-dot--personal"></span>
                Pins Pessoais
            </div>

            <div class="breakdown-card-value">{{ $stats['personal_pins'] }}</div>

            @php
                $pct = $stats['pins'] > 0 ? round(($stats['personal_pins'] / $stats['pins']) * 100) : 0;
            @endphp

            <div class="breakdown-bar">
                <div class="breakdown-bar-fill breakdown-bar--personal" style="width: {{ $pct }}%"></div>
            </div>

            <div class="breakdown-card-pct">{{ $pct }}% do total</div>
        </div>

        <div class="breakdown-card">
            <div class="breakdown-card-label">
                <span class="breakdown-dot breakdown-dot--group"></span>
                Pins de Grupo
            </div>

            <div class="breakdown-card-value">{{ $stats['group_pins'] }}</div>

            @php
                $pct2 = $stats['pins'] > 0 ? round(($stats['group_pins'] / $stats['pins']) * 100) : 0;
            @endphp

            <div class="breakdown-bar">
                <div class="breakdown-bar-fill breakdown-bar--group" style="width: {{ $pct2 }}%"></div>
            </div>

            <div class="breakdown-card-pct">{{ $pct2 }}% do total</div>
        </div>

        <div class="breakdown-card">
            <div class="breakdown-card-label">
                <span class="breakdown-dot breakdown-dot--admin"></span>
                Administradores
            </div>

            <div class="breakdown-card-value">{{ $stats['admins'] }}</div>

            @php
                $pct3 = $stats['users'] > 0 ? min(round(($stats['admins'] / $stats['users']) * 100), 100) : 0;
            @endphp

            <div class="breakdown-bar">
                <div class="breakdown-bar-fill breakdown-bar--admin" style="width: {{ $pct3 }}%"></div>
            </div>

            <div class="breakdown-card-pct">{{ $pct3 }}% dos utilizadores</div>
        </div>

    </div>
</section>

<div class="admin-two-col">

    <section class="admin-section">
        <div class="section-header">
            <h2 class="section-title">Utilizadores Recentes</h2>

            <a href="{{ route('admin.users') }}" class="section-link">
                Ver todos
            </a>
        </div>

        <div class="recent-list">
            @forelse($latestUsers as $user)
                <div class="recent-item">
                    <div class="recent-avatar {{ $user->is_admin ? 'recent-avatar--admin' : '' }}">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>

                    <div class="recent-body">
                        <span class="recent-name">
                            {{ $user->name }}

                            @if($user->is_admin)
                                <span class="recent-role-badge">Admin</span>
                            @endif
                        </span>

                        <span class="recent-sub">{{ $user->email }}</span>
                    </div>

                    <span class="recent-time">{{ $user->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="recent-empty">Ainda não existem utilizadores registados.</div>
            @endforelse
        </div>
    </section>

    <section class="admin-section">
        <div class="section-header">
            <h2 class="section-title">Pins Recentes</h2>

            <a href="{{ route('admin.pins') }}" class="section-link">
                Ver todos
            </a>
        </div>

        <div class="recent-list">
            @forelse($latestPins as $pin)
                <div class="recent-item">
                    <div class="recent-pin-icon {{ $pin->group_id ? 'recent-pin-icon--group' : 'recent-pin-icon--personal' }}">
                        <i class="bi bi-geo-alt"></i>
                    </div>

                    <div class="recent-body">
                        <span class="recent-name">{{ $pin->title ?? 'Sem título' }}</span>

                        <span class="recent-sub">
                            {{ $pin->user->name ?? 'Utilizador não identificado' }}
                            · {{ $pin->group_id ? 'Grupo' : 'Pessoal' }}

                            @if($pin->location_text)
                                · {{ $pin->location_text }}
                            @endif
                        </span>
                    </div>

                    <span class="recent-time">{{ $pin->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="recent-empty">Ainda não existem pins criados.</div>
            @endforelse
        </div>
    </section>

</div>

<div class="admin-two-col">

    <section class="admin-section">
        <div class="section-header">
            <h2 class="section-title">Grupos Recentes</h2>

            <a href="{{ route('admin.groups') }}" class="section-link">
                Ver todos
            </a>
        </div>

        <div class="recent-list">
            @forelse($latestGroups as $group)
                <div class="recent-item">
                    <div class="recent-pin-icon recent-pin-icon--group">
                        {{ strtoupper(substr($group->name, 0, 1)) }}
                    </div>

                    <div class="recent-body">
                        <span class="recent-name">{{ $group->name }}</span>

                        <span class="recent-sub">
                            {{ $group->users_count ?? 0 }} membros
                            · {{ $group->pins_count ?? 0 }} pins
                        </span>
                    </div>

                    <span class="recent-time">{{ $group->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="recent-empty">Ainda não existem grupos criados.</div>
            @endforelse
        </div>
    </section>

    <section class="admin-section">
        <div class="section-header">
            <h2 class="section-title">Eventos Recentes</h2>

            <a href="{{ route('admin.events') }}" class="section-link">
                Ver todos
            </a>
        </div>

        <div class="recent-list">
            @forelse(($latestEvents ?? collect()) as $event)
                <div class="recent-item">
                    <div class="recent-pin-icon recent-pin-icon--personal">
                        <i class="bi bi-calendar-event"></i>
                    </div>

                    <div class="recent-body">
                        <span class="recent-name">{{ $event->title ?? 'Sem título' }}</span>

                        <span class="recent-sub">
                            {{ $event->creator->name ?? 'Utilizador não identificado' }}

                            @if($event->location_text)
                                · {{ $event->location_text }}
                            @endif

                            · {{ $event->participants_count ?? 0 }} participantes
                        </span>
                    </div>

                    <span class="recent-time">{{ $event->created_at->diffForHumans() }}</span>
                </div>
            @empty
                <div class="recent-empty">Ainda não existem eventos criados.</div>
            @endforelse
        </div>
    </section>

</div>

@endsection