@extends('layouts.app')

@section('title', 'Eventos — Wander Jar')
@section('page-id', 'events.index')

@push('styles')
  @vite('resources/css/events-index.css')

  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    crossorigin=""
  >
@endpush

@push('scripts')
  <script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    crossorigin=""
  ></script>

  @vite('resources/js/events-index.js')
@endpush

@section('content')

<main class="events-page">
  <div class="container wj-narrow">

    <section class="wj-card events-hero" aria-labelledby="events-page-title">
      <div class="events-hero__left">
        <div class="events-card__head">
          <div>
            <p class="events-kicker">
              Comunidade
            </p>

            <h1 id="events-page-title" class="wj-title">
              Eventos
            </h1>

            <p class="wj-subtitle">
              Explora eventos futuros, consulta a localização no mapa e participa em experiências criadas pela comunidade.
            </p>
          </div>

          <span class="events-chip">
            <i class="bi bi-calendar2-event" aria-hidden="true"></i>
            <strong>{{ $events->total() }}</strong>
            <span>eventos</span>
          </span>
        </div>

        @if(($q ?? '') !== '' || ($date ?? 'all') !== 'all')
          <div class="events-chips" aria-label="Filtros ativos">
            @if(($q ?? '') !== '')
              <span class="events-chip">
                <i class="bi bi-search" aria-hidden="true"></i>
                <span>{{ $q }}</span>
              </span>
            @endif

            @if(($date ?? 'all') !== 'all')
              <span class="events-chip">
                <i class="bi bi-funnel" aria-hidden="true"></i>

                <span>
                  @if($date === 'today')
                    Hoje
                  @elseif($date === 'week')
                    Próximos 7 dias
                  @else
                    Filtro ativo
                  @endif
                </span>
              </span>
            @endif
          </div>
        @endif
      </div>

      <div class="events-hero__right">
        <a class="wj-btn wj-btn-primary" href="{{ route('events.create') }}">
          <i class="bi bi-plus-lg" aria-hidden="true"></i>
          <span>Criar evento</span>
        </a>
      </div>
    </section>

    <section class="wj-card events-filtercard" aria-labelledby="events-filter-title">
      <div class="events-filter-head">
        <div id="events-filter-title" class="events-filter-title">
          <i class="bi bi-funnel" aria-hidden="true"></i>
          <span>Filtrar eventos</span>
        </div>
      </div>

      <form class="events-filter-form" method="GET" action="{{ route('events.index') }}" role="search">
        <div class="events-filter-field grow">
          <i class="bi bi-search" aria-hidden="true"></i>

          <input
            class="events-input"
            type="text"
            name="q"
            value="{{ $q ?? request('q') }}"
            placeholder="Pesquisar por título ou local..."
            autocomplete="off"
            aria-label="Pesquisar eventos por título ou local"
          >
        </div>

        <div class="events-filter-field events-field--select">
          <i class="bi bi-calendar3" aria-hidden="true"></i>

          <select class="events-select" name="date" aria-label="Filtrar eventos por data">
            <option value="all" @selected(($date ?? 'all') === 'all')>
              Todas as datas
            </option>

            <option value="today" @selected(($date ?? '') === 'today')>
              Hoje
            </option>

            <option value="week" @selected(($date ?? '') === 'week')>
              Próximos 7 dias
            </option>
          </select>
        </div>

        <button class="wj-btn wj-btn-primary events-apply-btn" type="submit">
          <i class="bi bi-check2" aria-hidden="true"></i>
          <span>Aplicar</span>
        </button>

        @if(($q ?? '') !== '' || (($date ?? 'all') !== 'all'))
          <a class="wj-btn wj-btn-ghost" href="{{ route('events.index') }}">
            <i class="bi bi-x-lg" aria-hidden="true"></i>
            <span>Limpar</span>
          </a>
        @endif
      </form>
    </section>

    <section class="wj-card events-mapcard" aria-labelledby="events-map-title">
      <div class="events-card__head">
        <div id="events-map-title" class="events-card__title">
          <i class="bi bi-map" aria-hidden="true"></i>
          <span>Mapa de eventos</span>
        </div>

        <button
          class="wj-btn wj-btn-ghost"
          type="button"
          data-map-toggle
          aria-expanded="true"
        >
          <i class="bi bi-eye-slash" aria-hidden="true"></i>
          <span>Ocultar</span>
        </button>
      </div>

      <div data-map-wrap>
        <div
          id="eventsMap"
          class="events-map"
          aria-label="Mapa com a localização dos eventos"
        ></div>
      </div>
    </section>

    <div class="events-listhead">
      <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
      <span>Lista de eventos</span>
    </div>

    <section class="events-grid" aria-label="Lista de eventos">
      @forelse($events as $event)
        <a
          class="events-item wj-card"
          href="{{ route('events.show', $event) }}"
          data-event-card
          data-title="{{ e($event->title ?? 'Evento') }}"
          data-location="{{ e($event->location_text ?? '') }}"
          data-date="{{ $event->event_date ? $event->event_date->format('d/m/Y') : '' }}"
          data-time="{{ $event->event_time ? substr($event->event_time, 0, 5) : '' }}"
          data-url="{{ route('events.show', $event) }}"
          data-lat="{{ $event->lat }}"
          data-lng="{{ $event->lng }}"
        >
          <div class="events-item__top">
            <div class="events-item__title">
              {{ $event->title }}
            </div>

            <span class="events-badge">
              <i class="bi bi-calendar-event" aria-hidden="true"></i>
              <span>{{ $event->event_date?->format('d/m/Y') ?? 'Sem data' }}</span>
            </span>
          </div>

          <div class="events-item__row">
            <i class="bi bi-geo-alt" aria-hidden="true"></i>
            <span>{{ $event->location_text ?? 'Sem localização' }}</span>
          </div>

          <div class="events-item__meta">
            <span>
              <i class="bi bi-clock" aria-hidden="true"></i>
              {{ $event->event_time ? substr($event->event_time, 0, 5) : '—' }}
            </span>

            <span class="events-dot" aria-hidden="true">•</span>

            <span>
              <i class="bi bi-person" aria-hidden="true"></i>
              {{ $event->creator?->name ?? '—' }}
            </span>
          </div>
        </a>
      @empty
        <div class="events-emptycard wj-card">
          <div class="events-emptyicon" aria-hidden="true">
            <i class="bi bi-calendar2-x"></i>
          </div>

          <h3 class="events-emptytitle">
            Sem eventos disponíveis
          </h3>

          <p class="events-emptytext">
            Quando criares ou encontrares eventos futuros, eles aparecem aqui.
          </p>

          <a class="wj-btn wj-btn-primary" href="{{ route('events.create') }}">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            <span>Criar evento</span>
          </a>
        </div>
      @endforelse
    </section>

    @if(method_exists($events, 'links'))
      <div class="events-pagination">
        {{ $events->withQueryString()->links() }}
      </div>
    @endif

  </div>
</main>

@endsection