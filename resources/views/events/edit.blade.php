@extends('layouts.app')

@section('title', 'Editar evento — Wander Jar')
@section('page-id', 'events.edit')

@push('styles')
  @vite('resources/css/events-edit.css')

  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    crossorigin=""
  >
@endpush

@section('content')

@php
  $eventDate = old('event_date', $event->event_date ? \Carbon\Carbon::parse($event->event_date)->format('Y-m-d') : '');
  $eventTime = old('event_time', $event->event_time ? \Carbon\Carbon::parse($event->event_time)->format('H:i') : '');
@endphp

<main class="events-edit-page">
  <div class="container wj-narrow">

    <header class="events-edit-hero">
      <div class="events-edit-hero__left">
        <div class="events-edit-kicker">
          <i class="bi bi-pencil-square" aria-hidden="true"></i>
          <span>Editar evento</span>
        </div>

        <h1 class="wj-title">
          Editar evento
        </h1>

        <p class="wj-subtitle">
          Atualiza os detalhes do evento, incluindo data, hora, localização e limite de participantes.
        </p>
      </div>

      <div class="events-edit-hero__right">
        <a href="{{ route('events.show', $event) }}" class="wj-btn wj-btn-ghost">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
          <span>Voltar ao evento</span>
        </a>
      </div>
    </header>

    @if ($errors->any())
      <div class="wj-errorbox" role="alert">
        <strong>Há erros no formulário:</strong>

        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="events-edit-grid">

      <section class="events-edit-card" aria-labelledby="event-edit-form-title">
        <div class="events-edit-card__head">
          <div>
            <h2 id="event-edit-form-title" class="events-edit-card__title">
              Detalhes do evento
            </h2>

            <p class="events-edit-card__subtitle">
              Revê e ajusta as informações principais antes de guardar as alterações.
            </p>
          </div>
        </div>

        <form
          method="POST"
          action="{{ route('events.update', $event) }}"
          class="events-edit-form"
          id="eventEditForm"
        >
          @csrf
          @method('PATCH')

          <div class="events-edit-field">
            <label for="title">
              Título
            </label>

            <input
              type="text"
              id="title"
              name="title"
              class="events-edit-input @error('title') is-invalid @enderror"
              maxlength="120"
              required
              value="{{ old('title', $event->title) }}"
              placeholder="Ex: Caminhada ao pôr do sol"
              autocomplete="off"
            >

            @error('title')
              <small class="events-edit-error">{{ $message }}</small>
            @enderror
          </div>

          <div class="events-edit-field">
            <label for="description">
              Descrição
              <span class="events-edit-label-muted">(opcional)</span>
            </label>

            <textarea
              id="description"
              name="description"
              class="events-edit-input events-edit-textarea @error('description') is-invalid @enderror"
              rows="5"
              maxlength="5000"
              placeholder="Descreve o objetivo do evento, o que os participantes devem esperar e qualquer detalhe importante."
            >{{ old('description', $event->description) }}</textarea>

            @error('description')
              <small class="events-edit-error">{{ $message }}</small>
            @enderror
          </div>

          <div class="events-edit-row">
            <div class="events-edit-field">
              <label for="event_date">
                Data
              </label>

              <input
                type="date"
                id="event_date"
                name="event_date"
                class="events-edit-input @error('event_date') is-invalid @enderror"
                required
                value="{{ $eventDate }}"
              >

              @error('event_date')
                <small class="events-edit-error">{{ $message }}</small>
              @enderror
            </div>

            <div class="events-edit-field">
              <label for="event_time">
                Hora
              </label>

              <input
                type="time"
                id="event_time"
                name="event_time"
                class="events-edit-input @error('event_time') is-invalid @enderror"
                value="{{ $eventTime }}"
              >

              @error('event_time')
                <small class="events-edit-error">{{ $message }}</small>
              @enderror
            </div>
          </div>

          <div class="events-edit-field">
            <label for="max_participants">
              Limite de participantes
            </label>

            <input
              type="number"
              id="max_participants"
              name="max_participants"
              class="events-edit-input @error('max_participants') is-invalid @enderror"
              min="1"
              max="1000"
              required
              value="{{ old('max_participants', $event->max_participants) }}"
            >

            <small class="events-edit-help">
              Define quantas pessoas podem participar neste evento.
            </small>

            @error('max_participants')
              <small class="events-edit-error">{{ $message }}</small>
            @enderror
          </div>

          <div class="events-edit-field">
            <label for="location_text">
              Localização
            </label>

            <input
              type="text"
              id="location_text"
              name="location_text"
              class="events-edit-input @error('location_text') is-invalid @enderror"
              maxlength="180"
              value="{{ old('location_text', $event->location_text) }}"
              placeholder="Ex: Praia da Nazaré"
              autocomplete="off"
            >

            <small class="events-edit-help">
              Atualiza o nome do local ou escolhe uma nova posição no mapa.
            </small>

            @error('location_text')
              <small class="events-edit-error">{{ $message }}</small>
            @enderror
          </div>

          <div class="events-edit-field">
            <label for="mapSearch">
              Local no mapa
            </label>

            <div class="events-edit-mapbar">
              <input
                type="text"
                id="mapSearch"
                class="events-edit-input events-edit-mapsearch"
                placeholder="Pesquisar no mapa… Ex: Leiria, Porto, Lisboa"
                autocomplete="off"
              >

              <button
                type="button"
                id="mapSearchBtn"
                class="wj-btn wj-btn-ghost"
              >
                <i class="bi bi-search" aria-hidden="true"></i>
                <span>Procurar</span>
              </button>

              <button
                type="button"
                id="useMyLocation"
                class="wj-btn wj-btn-ghost"
              >
                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                <span>Usar localização</span>
              </button>

              <button
                type="button"
                id="clearLocation"
                class="wj-btn wj-btn-ghost"
              >
                <i class="bi bi-x-circle" aria-hidden="true"></i>
                <span>Limpar</span>
              </button>
            </div>

            <div
              id="eventMap"
              class="events-edit-map"
              data-lat="{{ old('lat', $event->lat) }}"
              data-lng="{{ old('lng', $event->lng) }}"
              data-title="{{ old('title', $event->title) }}"
              data-location="{{ old('location_text', $event->location_text) }}"
              aria-label="Mapa para alterar a localização do evento"
            ></div>

            <small id="coordsHint" class="events-edit-help">
              @if(old('lat', $event->lat) && old('lng', $event->lng))
                {{ old('lat', $event->lat) }}, {{ old('lng', $event->lng) }}
              @else
                Clica no mapa para marcar a localização do evento.
              @endif
            </small>

            @error('lat')
              <small class="events-edit-error">{{ $message }}</small>
            @enderror

            @error('lng')
              <small class="events-edit-error">{{ $message }}</small>
            @enderror
          </div>

          <input
            type="hidden"
            name="lat"
            id="lat"
            value="{{ old('lat', $event->lat) }}"
          >

          <input
            type="hidden"
            name="lng"
            id="lng"
            value="{{ old('lng', $event->lng) }}"
          >

          <div class="events-edit-actions">
            <a href="{{ route('events.show', $event) }}" class="wj-btn wj-btn-ghost">
              <i class="bi bi-x-lg" aria-hidden="true"></i>
              <span>Cancelar</span>
            </a>

            <button type="submit" class="wj-btn wj-btn-primary">
              <i class="bi bi-check2-circle" aria-hidden="true"></i>
              <span>Guardar alterações</span>
            </button>
          </div>
        </form>
      </section>

      <aside class="events-edit-card events-edit-card--side" aria-labelledby="event-edit-summary-title">
        <div class="events-edit-side-icon" aria-hidden="true">
          <i class="bi bi-calendar2-event"></i>
        </div>

        <h2 id="event-edit-summary-title" class="events-edit-card__title">
          Resumo
        </h2>

        <p class="events-edit-card__subtitle">
          Confirma os dados principais antes de guardar.
        </p>

        <div class="events-edit-summary">
          <div class="events-edit-summary__item">
            <span>
              <i class="bi bi-calendar-event" aria-hidden="true"></i>
              Data
            </span>

            <strong id="summaryDate">
              {{ $eventDate ?: '—' }}
            </strong>
          </div>

          <div class="events-edit-summary__item">
            <span>
              <i class="bi bi-clock" aria-hidden="true"></i>
              Hora
            </span>

            <strong id="summaryTime">
              {{ $eventTime ?: '—' }}
            </strong>
          </div>

          <div class="events-edit-summary__item">
            <span>
              <i class="bi bi-people" aria-hidden="true"></i>
              Participantes
            </span>

            <strong id="summaryParticipants">
              {{ old('max_participants', $event->max_participants) }}
            </strong>
          </div>

          <div class="events-edit-summary__item">
            <span>
              <i class="bi bi-geo-alt" aria-hidden="true"></i>
              Local
            </span>

            <strong id="summaryLocation">
              {{ old('location_text', $event->location_text) ?: '—' }}
            </strong>
          </div>
        </div>

        <p class="events-edit-side-note">
          <i class="bi bi-info-circle" aria-hidden="true"></i>
          As alterações ficam visíveis na página pública do evento após guardares.
        </p>
      </aside>

    </div>

  </div>
</main>

@endsection

@push('scripts')
  <script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    crossorigin=""
  ></script>

  @vite('resources/js/events-edit.js')
@endpush