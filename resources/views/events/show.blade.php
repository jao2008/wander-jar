@extends('layouts.app')

@section('title', ($event->title ?? 'Evento') . ' — Wander Jar')
@section('page-id', 'events.show')

@push('styles')
  @vite('resources/css/events-show.css')

  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    crossorigin=""
  />
@endpush

@section('content')
<section id="eventsShow">
  <div class="wj-narrow">

    <div class="event-topbar">
      <a class="event-back" href="{{ route('events.index') }}">
        <i class="bi bi-arrow-left" aria-hidden="true"></i>
        <span>Voltar aos eventos</span>
      </a>
    </div>

    <header class="event-hero">
      <div class="event-kicker">
        <i class="bi bi-calendar2-event" aria-hidden="true"></i>
        <span>Detalhes do evento</span>
      </div>

      <h1 class="event-title">
        {{ $event->title ?? 'Sem título' }}
      </h1>

      <div class="event-subline">
        <span>
          <i class="bi bi-geo-alt" aria-hidden="true"></i>
          {{ $event->location_text ?? 'Sem localização definida' }}
        </span>

        <span aria-hidden="true">•</span>

        <span>
          <i class="bi bi-calendar-event" aria-hidden="true"></i>
          {{ optional($event->event_date)->format('d/m/Y') ?? $event->event_date ?? 'Sem data definida' }}
        </span>

        <span aria-hidden="true">•</span>

        <span>
          <i class="bi bi-clock" aria-hidden="true"></i>
          {{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : 'Sem hora definida' }}
        </span>
      </div>

      <div class="event-pills" aria-label="Estado e participação no evento">
        <span class="event-pill">
          <i class="bi bi-people" aria-hidden="true"></i>
          <span>{{ $participantsCount }} / {{ $event->max_participants }} participantes</span>
        </span>

        @if($isActive)
          <span class="event-pill event-pill--ok">
            <i class="bi bi-check-circle" aria-hidden="true"></i>
            <span>Ativo</span>
          </span>
        @else
          <span class="event-pill event-pill--off">
            <i class="bi bi-x-circle" aria-hidden="true"></i>
            <span>Inativo ou terminado</span>
          </span>
        @endif

        @if($isCreator)
          <span class="event-pill event-pill--warn">
            <i class="bi bi-star" aria-hidden="true"></i>
            <span>Criado por ti</span>
          </span>
        @elseif($isJoined)
          <span class="event-pill event-pill--ok">
            <i class="bi bi-check2-circle" aria-hidden="true"></i>
            <span>Estás a participar</span>
          </span>
        @endif
      </div>

      <div class="event-actions">
        @if($isCreator)
          <a class="wj-btn wj-btn-primary" href="{{ route('events.edit', $event) }}">
            <i class="bi bi-pencil-square" aria-hidden="true"></i>
            <span>Editar evento</span>
          </a>
        @endif

        @if(!$isActive)
          <button type="button" class="wj-btn wj-btn-ghost" disabled>
            <i class="bi bi-lock" aria-hidden="true"></i>
            <span>Evento indisponível</span>
          </button>
        @elseif($isCreator)
          <button type="button" class="wj-btn wj-btn-ghost" disabled>
            <i class="bi bi-person-check" aria-hidden="true"></i>
            <span>És o criador</span>
          </button>
        @elseif($isJoined)
          <form method="POST" action="{{ route('events.leave', $event) }}">
            @csrf

            <button type="submit" class="wj-btn wj-btn-danger">
              <i class="bi bi-box-arrow-left" aria-hidden="true"></i>
              <span>Sair do evento</span>
            </button>
          </form>
        @else
          <form method="POST" action="{{ route('events.join', $event) }}">
            @csrf

            <button type="submit" class="wj-btn wj-btn-primary">
              <i class="bi bi-check2-circle" aria-hidden="true"></i>
              <span>Participar no evento</span>
            </button>
          </form>
        @endif
      </div>
    </header>

    <div class="event-grid">
      <article class="event-card" aria-labelledby="event-description-title">
        <div class="event-card__head">
          <h2 id="event-description-title" class="event-card__title">
            <i class="bi bi-card-text" aria-hidden="true"></i>
            <span>Descrição</span>
          </h2>
        </div>

        <div class="event-text">
          {!! nl2br(e($event->description ?? 'Sem descrição.')) !!}
        </div>
      </article>

      <aside class="event-card" aria-labelledby="event-details-title">
        <div class="event-card__head">
          <h2 id="event-details-title" class="event-card__title">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <span>Detalhes</span>
          </h2>

          <span class="event-badge">
            {{ $isActive ? 'Ativo' : 'Inativo' }}
          </span>
        </div>

        <div class="event-muted">
          <p>
            <strong>Data:</strong>
            <span>{{ optional($event->event_date)->format('d/m/Y') ?? $event->event_date ?? 'Sem data definida' }}</span>
          </p>

          <p>
            <strong>Hora:</strong>
            <span>{{ $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : 'Sem hora definida' }}</span>
          </p>

          <p>
            <strong>Criado por:</strong>
            <span>{{ $event->creator->name ?? 'Utilizador desconhecido' }}</span>
          </p>

          <p>
            <strong>Participantes:</strong>
            <span>{{ $participantsCount }} / {{ $event->max_participants }}</span>
          </p>
        </div>
      </aside>
    </div>

    @if(!is_null($event->lat) && !is_null($event->lng))
      <div class="event-locationline">
        <i class="bi bi-geo-alt" aria-hidden="true"></i>
        <span>{{ $event->location_text ?? 'Localização do evento' }}</span>
        <span aria-hidden="true">•</span>
        <span>{{ $event->lat }}, {{ $event->lng }}</span>
      </div>

      <section class="event-mapcard" aria-labelledby="event-map-title">
        <div class="event-card__head event-mapcard__head">
          <h2 id="event-map-title" class="event-card__title">
            <i class="bi bi-map" aria-hidden="true"></i>
            <span>Localização no mapa</span>
          </h2>
        </div>

        <div
          id="eventShowMap"
          class="event-map"
          data-lat="{{ $event->lat }}"
          data-lng="{{ $event->lng }}"
          data-title="{{ $event->title ?? 'Evento' }}"
          data-location="{{ $event->location_text ?? '' }}"
          aria-label="Mapa com a localização do evento"
        ></div>
      </section>
    @endif

  </div>
</section>
@endsection

@push('scripts')
  <script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    crossorigin=""
  ></script>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const mapEl = document.getElementById('eventShowMap');

      if (!mapEl || typeof L === 'undefined') {
        return;
      }

      const lat = Number(mapEl.dataset.lat);
      const lng = Number(mapEl.dataset.lng);
      const title = mapEl.dataset.title || 'Evento';
      const location = mapEl.dataset.location || '';

      if (!Number.isFinite(lat) || !Number.isFinite(lng)) {
        return;
      }

      const map = L.map(mapEl, {
        zoomControl: true,
        scrollWheelZoom: true,
      }).setView([lat, lng], 14);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap',
      }).addTo(map);

      const marker = L.marker([lat, lng]).addTo(map);

      marker.bindPopup(`
        <strong>${title}</strong>
        ${location ? `<br><span>${location}</span>` : ''}
      `);

      setTimeout(() => {
        map.invalidateSize();
      }, 250);
    });
  </script>
@endpush