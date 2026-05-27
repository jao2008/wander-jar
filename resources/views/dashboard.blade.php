@extends('layouts.app')

@section('title', 'Dashboard — Wander Jar')
@section('page-id', 'dashboard')

@push('styles')
  @vite('resources/css/dashboard.css')
@endpush

@section('content')

@php
  $user = auth()->user();

  $totalPins = $totalPins ?? 0;
  $totalGroups = $totalGroups ?? 0;
  $totalEvents = $totalEvents ?? 0;
  $totalMessages = $totalMessages ?? 0;

  $groups = collect($groups ?? []);
  $recentPins = collect($recentPins ?? $pins ?? []);
  $upcomingEvents = collect($upcomingEvents ?? $events ?? []);

  $activityChart = $activityChart ?? [
    'range_label' => 'Últimos meses',
    'total' => 0,
    'max' => 0,
    'points' => [],
  ];

  $activityPoints = collect($activityChart['points'] ?? []);

  $initial = $user
    ? mb_strtoupper(mb_substr($user->name ?? 'U', 0, 1))
    : '?';

  $formatDate = function ($value) {
      if (!$value) {
          return null;
      }

      try {
          return \Carbon\Carbon::parse($value)->format('d/m/Y');
      } catch (\Throwable $e) {
          return null;
      }
  };

  $eventDate = function ($event) use ($formatDate) {
      return $formatDate(
          $event->start_at
          ?? $event->starts_at
          ?? $event->event_date
          ?? $event->date
          ?? $event->created_at
          ?? null
      );
  };
@endphp

<main class="dashboard-page dash">
  <div class="dashboard-container">

    <header class="dash-hero">
      <div class="dash-hero-left">
        <div class="user-avatar" aria-hidden="true">
          @if($user && $user->profile_photo)
            <img
              src="{{ route('profile.photo.show', $user->id) }}?v={{ $user->updated_at?->timestamp }}"
              alt=""
              class="dash-avatar-img"
              loading="lazy"
              decoding="async"
              onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
            >

            <span class="dash-avatar-fallback" style="display:none;">
              {{ $initial }}
            </span>
          @else
            <span class="dash-avatar-fallback">
              {{ $initial }}
            </span>
          @endif
        </div>

        <div class="dash-hero-copy">
          <p class="dash-kicker">
            Dashboard
          </p>

          <h1 class="dash-title">
            Olá{{ $user ? ', ' . $user->name : '' }}!
          </h1>

          <p class="dash-subtitle">
            Gere os teus mapas, grupos, eventos e memórias num só lugar.
          </p>
        </div>
      </div>

      <div class="dash-hero-actions" aria-label="Ações rápidas">
        <a class="dash-btn dash-btn-primary" href="{{ route('pins.create') }}">
          <i class="bi bi-plus-lg" aria-hidden="true"></i>
          <span>Novo pin</span>
        </a>

        <a class="dash-btn dash-btn-secondary" href="{{ route('events.create') }}">
          <i class="bi bi-calendar-plus" aria-hidden="true"></i>
          <span>Criar evento</span>
        </a>

        <a class="dash-btn dash-btn-secondary" href="{{ route('groups.create') }}">
          <i class="bi bi-people" aria-hidden="true"></i>
          <span>Criar grupo</span>
        </a>
      </div>
    </header>

    <section class="dash-stats" aria-label="Resumo da conta">
      <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">
          <i class="bi bi-pin-map-fill"></i>
        </div>

        <div class="stat-content">
          <strong class="stat-number" data-target="{{ $totalPins }}">
            {{ $totalPins }}
          </strong>

          <span class="stat-label">
            Pins
          </span>
        </div>
      </article>

      <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">
          <i class="bi bi-people-fill"></i>
        </div>

        <div class="stat-content">
          <strong class="stat-number" data-target="{{ $totalGroups }}">
            {{ $totalGroups }}
          </strong>

          <span class="stat-label">
            Grupos
          </span>
        </div>
      </article>

      <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">
          <i class="bi bi-calendar-event-fill"></i>
        </div>

        <div class="stat-content">
          <strong class="stat-number" data-target="{{ $totalEvents }}">
            {{ $totalEvents }}
          </strong>

          <span class="stat-label">
            Eventos
          </span>
        </div>
      </article>

      <article class="stat-card">
        <div class="stat-icon" aria-hidden="true">
          <i class="bi bi-chat-dots-fill"></i>
        </div>

        <div class="stat-content">
          <strong class="stat-number" data-target="{{ $totalMessages }}">
            {{ $totalMessages }}
          </strong>

          <span class="stat-label">
            Mensagens
          </span>
        </div>
      </article>
    </section>

    <section class="dash-activity" aria-label="Atividade recente">
      <article class="dash-section dash-activity-card">
        <div class="section-header dash-activity-head">
          <div>
            <p class="section-kicker">
              Atividade
            </p>

            <h2 class="section-title">
              Pins criados por mês
            </h2>
          </div>

          <div class="dash-activity-meta">
            <span class="dash-activity-chip">
              <i class="bi bi-activity" aria-hidden="true"></i>
              {{ $activityChart['range_label'] ?? 'Últimos meses' }}
            </span>

            <span class="dash-activity-total">
              <strong>{{ $activityChart['total'] ?? 0 }}</strong>
              pins
            </span>
          </div>
        </div>

        @if($activityPoints->isNotEmpty())
          <div
            class="activity-chart"
            id="dashActivityChart"
            data-points='@json($activityPoints->values())'
            data-max="{{ $activityChart['max'] ?? 0 }}"
          >
            @foreach($activityPoints as $point)
              @php
                $value = (int) ($point['value'] ?? 0);
                $height = (int) ($point['height'] ?? 0);
                $label = $point['label'] ?? '';
                $fullLabel = $point['full_label'] ?? $label;
              @endphp

              <div
                class="activity-bar-item {{ $value > 0 ? 'has-value' : 'is-empty' }}"
                title="{{ $fullLabel }} — {{ $value }} pins"
              >
                <div class="activity-bar-wrap">
                  <span class="activity-bar-value">
                    {{ $value }}
                  </span>

                  <div
                    class="activity-bar {{ $value > 0 ? 'has-value' : 'is-empty' }}"
                    style="--bar-height: {{ max($height, 8) }}%;"
                    data-value="{{ $value }}"
                  ></div>
                </div>

                <span class="activity-bar-label">
                  {{ $label }}
                </span>
              </div>
            @endforeach
          </div>
        @else
          <div class="dash-empty dash-empty--chart">
            <div class="dash-empty__icon" aria-hidden="true">
              <i class="bi bi-bar-chart"></i>
            </div>

            <div>
              <h3 class="dash-empty__title">
                Ainda não há dados suficientes
              </h3>

              <p class="dash-empty__text">
                Quando criares pins, a tua atividade mensal aparece aqui.
              </p>
            </div>
          </div>
        @endif
      </article>
    </section>

    <section class="dash-quicknav" aria-label="Acessos rápidos">
      <a class="quick-card" href="{{ route('map') }}">
        <div class="quick-icon" aria-hidden="true">
          <i class="bi bi-pin-map"></i>
        </div>

        <div class="quick-text">
          <strong class="quick-title">
            Mapa pessoal
          </strong>

          <span class="quick-sub">
            Consulta os teus pins privados
          </span>
        </div>

        <i class="bi bi-arrow-right short-arrow" aria-hidden="true"></i>
      </a>

      <a class="quick-card" href="{{ route('pins.index') }}">
        <div class="quick-icon" aria-hidden="true">
          <i class="bi bi-geo-alt"></i>
        </div>

        <div class="quick-text">
          <strong class="quick-title">
            Pins
          </strong>

          <span class="quick-sub">
            Vê todas as tuas memórias
          </span>
        </div>

        <i class="bi bi-arrow-right short-arrow" aria-hidden="true"></i>
      </a>

      <a class="quick-card" href="{{ route('groups.index') }}">
        <div class="quick-icon" aria-hidden="true">
          <i class="bi bi-people"></i>
        </div>

        <div class="quick-text">
          <strong class="quick-title">
            Grupos
          </strong>

          <span class="quick-sub">
            Mapas partilhados
          </span>
        </div>

        <i class="bi bi-arrow-right short-arrow" aria-hidden="true"></i>
      </a>

      <a class="quick-card" href="{{ route('events.index') }}">
        <div class="quick-icon" aria-hidden="true">
          <i class="bi bi-calendar-event"></i>
        </div>

        <div class="quick-text">
          <strong class="quick-title">
            Eventos
          </strong>

          <span class="quick-sub">
            Planeia experiências
          </span>
        </div>

        <i class="bi bi-arrow-right short-arrow" aria-hidden="true"></i>
      </a>

      <a class="quick-card" href="{{ route('profile.edit') }}">
        <div class="quick-icon" aria-hidden="true">
          <i class="bi bi-person-circle"></i>
        </div>

        <div class="quick-text">
          <strong class="quick-title">
            Perfil
          </strong>

          <span class="quick-sub">
            Gere a tua conta
          </span>
        </div>

        <i class="bi bi-arrow-right short-arrow" aria-hidden="true"></i>
      </a>
    </section>

    <section class="dash-content-grid" aria-label="Resumo detalhado">

      <article class="dash-section dash-groups">
        <div class="section-header">
          <div>
            <p class="section-kicker">
              Grupos
            </p>

            <h2 class="section-title">
              Os teus grupos
            </h2>
          </div>

          <a href="{{ route('groups.index') }}" class="section-link">
            <span>Ver todos</span>
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
          </a>
        </div>

        @if($groups->isNotEmpty())
          <div class="jars-grid">
            @foreach($groups->take(4) as $group)
              @php
                $role = $group->pivot->role ?? $group->role ?? 'member';
                $isAdmin = $role === 'admin';

                $membersCount = $group->members_count
                  ?? $group->users_count
                  ?? $group->members?->count()
                  ?? 0;

                $pinsCount = $group->pins_count
                  ?? $group->pins?->count()
                  ?? 0;
              @endphp

              <article class="jar-card">
                <div class="jar-header">
                  <div class="jar-icon" aria-hidden="true">
                    <i class="bi bi-people"></i>
                  </div>

                  <span class="jar-badge {{ $isAdmin ? 'is-admin' : '' }}">
                    <i class="bi {{ $isAdmin ? 'bi-shield-check' : 'bi-person' }}" aria-hidden="true"></i>
                    {{ $isAdmin ? 'Admin' : 'Membro' }}
                  </span>
                </div>

                <h3 class="jar-title">
                  {{ $group->name ?? 'Grupo sem nome' }}
                </h3>

                <p class="jar-meta">
                  <span>{{ $membersCount }} membros</span>
                  <span class="jar-dot">•</span>
                  <span>{{ $pinsCount }} pins</span>
                </p>

                <div class="jar-actions-group">
                  <a href="{{ route('groups.map', $group) }}" class="btn-jar-map">
                    <i class="bi bi-map" aria-hidden="true"></i>
                    <span>Mapa</span>
                  </a>

                  @if(Route::has('groups.chat'))
                    <a href="{{ route('groups.chat', $group) }}" class="btn-jar-chat">
                      <i class="bi bi-chat-dots" aria-hidden="true"></i>
                      <span>Chat</span>
                    </a>
                  @else
                    <a href="{{ route('groups.index') }}" class="btn-jar-chat">
                      <i class="bi bi-arrow-right" aria-hidden="true"></i>
                      <span>Abrir</span>
                    </a>
                  @endif
                </div>
              </article>
            @endforeach
          </div>
        @else
          <div class="dash-empty">
            <div class="dash-empty__icon" aria-hidden="true">
              <i class="bi bi-people"></i>
            </div>

            <div>
              <h3 class="dash-empty__title">
                Ainda não tens grupos
              </h3>

              <p class="dash-empty__text">
                Cria um grupo para partilhares pins, mapas e experiências com outras pessoas.
              </p>

              <a href="{{ route('groups.create') }}" class="dash-empty__link">
                Criar grupo
              </a>
            </div>
          </div>
        @endif
      </article>

      <article class="dash-section">
        <div class="section-header">
          <div>
            <p class="section-kicker">
              Pins
            </p>

            <h2 class="section-title">
              Pins recentes
            </h2>
          </div>

          <a href="{{ route('pins.index') }}" class="section-link">
            <span>Ver todos</span>
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
          </a>
        </div>

        @if($recentPins->isNotEmpty())
          <div class="dash-list">
            @foreach($recentPins->take(5) as $pin)
              <a href="{{ route('pins.show', $pin) }}" class="dash-list-item">
                <div class="dash-list-icon" aria-hidden="true">
                  <i class="bi bi-geo-alt"></i>
                </div>

                <div class="dash-list-text">
                  <strong>
                    {{ $pin->title ?? 'Pin sem título' }}
                  </strong>

                  <span>
                    {{ $pin->location_text ?? $pin->location ?? 'Sem localização definida' }}
                  </span>
                </div>

                <div class="dash-list-arrow" aria-hidden="true">
                  <i class="bi bi-arrow-right"></i>
                </div>
              </a>
            @endforeach
          </div>
        @else
          <div class="dash-empty">
            <div class="dash-empty__icon" aria-hidden="true">
              <i class="bi bi-pin-map"></i>
            </div>

            <div>
              <h3 class="dash-empty__title">
                Ainda não tens pins
              </h3>

              <p class="dash-empty__text">
                Guarda a tua primeira memória no mapa.
              </p>

              <a href="{{ route('pins.create') }}" class="dash-empty__link">
                Criar pin
              </a>
            </div>
          </div>
        @endif
      </article>

      <article class="dash-section">
        <div class="section-header">
          <div>
            <p class="section-kicker">
              Eventos
            </p>

            <h2 class="section-title">
              Próximos eventos
            </h2>
          </div>

          <a href="{{ route('events.index') }}" class="section-link">
            <span>Ver todos</span>
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
          </a>
        </div>

        @if($upcomingEvents->isNotEmpty())
          <div class="dash-list">
            @foreach($upcomingEvents->take(5) as $event)
              <a href="{{ route('events.show', $event) }}" class="dash-list-item">
                <div class="dash-list-icon" aria-hidden="true">
                  <i class="bi bi-calendar-event"></i>
                </div>

                <div class="dash-list-text">
                  <strong>
                    {{ $event->title ?? $event->name ?? 'Evento sem título' }}
                  </strong>

                  <span>
                    {{ $eventDate($event) ?? 'Data por definir' }}
                  </span>
                </div>

                <div class="dash-list-arrow" aria-hidden="true">
                  <i class="bi bi-arrow-right"></i>
                </div>
              </a>
            @endforeach
          </div>
        @else
          <div class="dash-empty">
            <div class="dash-empty__icon" aria-hidden="true">
              <i class="bi bi-calendar-plus"></i>
            </div>

            <div>
              <h3 class="dash-empty__title">
                Ainda não tens eventos
              </h3>

              <p class="dash-empty__text">
                Cria eventos para planear viagens, encontros ou experiências.
              </p>

              <a href="{{ route('events.create') }}" class="dash-empty__link">
                Criar evento
              </a>
            </div>
          </div>
        @endif
      </article>

    </section>

  </div>
</main>

@endsection

@push('scripts')
  @vite('resources/js/dashboard.js')
@endpush