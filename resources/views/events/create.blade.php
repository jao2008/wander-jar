@extends('layouts.app')

@section('title', 'Criar evento — Wander Jar')
@section('page-id', 'events.create')

@push('styles')
  @vite('resources/css/events-create.css')

  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    crossorigin=""
  >

  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css"
  >
@endpush

@section('content')

@php
  $today = now()->toDateString();
@endphp

<main class="events-create-page">
  <div class="container wj-narrow">

    <header class="events-create-hero">
      <div class="events-create-hero__left">
        <div class="events-create-kicker">
          <i class="bi bi-calendar-plus" aria-hidden="true"></i>
          <span>Novo evento</span>
        </div>

        <h1 class="wj-title">
          Criar evento
        </h1>

        <p class="wj-subtitle">
          Cria uma experiência aberta à comunidade, define a data, hora, localização e limite de participantes.
        </p>
      </div>

      <div class="events-create-hero__right">
        <a class="wj-btn wj-btn-ghost" href="{{ route('events.index') }}">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
          <span>Voltar aos eventos</span>
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

    <div class="events-create-grid">

      <section class="events-create-card" aria-labelledby="event-create-form-title">
        <div class="events-create-card__head">
          <div>
            <h2 id="event-create-form-title" class="events-create-card__title">
              Detalhes do evento
            </h2>

            <p class="events-create-card__subtitle">
              Preenche as informações principais para publicar o evento.
            </p>
          </div>
        </div>

        <form
          method="POST"
          action="{{ route('events.store') }}"
          class="events-create-form"
          id="eventCreateForm"
        >
          @csrf

          <div class="events-create-field">
            <label for="title">
              Título
            </label>

            <input
              type="text"
              id="title"
              name="title"
              class="events-create-input @error('title') is-invalid @enderror"
              value="{{ old('title') }}"
              maxlength="120"
              required
              placeholder="Ex: Caminhada ao pôr do sol"
              autocomplete="off"
            >

            @error('title')
              <small class="events-create-error">{{ $message }}</small>
            @enderror
          </div>

          <div class="events-create-field">
            <label for="description">
              Descrição
              <span class="events-create-label-muted">(opcional)</span>
            </label>

            <textarea
              id="description"
              name="description"
              class="events-create-input events-create-textarea @error('description') is-invalid @enderror"
              rows="5"
              maxlength="5000"
              placeholder="Descreve o objetivo do evento, o que os participantes devem esperar e qualquer detalhe importante."
            >{{ old('description') }}</textarea>

            @error('description')
              <small class="events-create-error">{{ $message }}</small>
            @enderror
          </div>

          <div class="events-create-row">
            <div class="events-create-field">
              <label for="event_date">
                Data
              </label>

              <input
                type="text"
                id="event_date"
                name="event_date"
                class="events-create-input @error('event_date') is-invalid @enderror"
                value="{{ old('event_date') }}"
                data-min="{{ $today }}"
                required
                placeholder="Selecionar data"
                autocomplete="off"
              >

              @error('event_date')
                <small class="events-create-error">{{ $message }}</small>
              @enderror
            </div>

            <div class="events-create-field">
              <label for="event_time">
                Hora
              </label>

              <input
                type="text"
                id="event_time"
                name="event_time"
                class="events-create-input @error('event_time') is-invalid @enderror"
                value="{{ old('event_time') }}"
                required
                placeholder="Selecionar hora"
                autocomplete="off"
              >

              @error('event_time')
                <small class="events-create-error">{{ $message }}</small>
              @enderror
            </div>
          </div>

          <div class="events-create-field">
            <label for="max_participants">
              Limite de participantes
            </label>

            <input
              type="number"
              id="max_participants"
              name="max_participants"
              class="events-create-input @error('max_participants') is-invalid @enderror"
              value="{{ old('max_participants', 10) }}"
              min="1"
              max="1000"
              required
            >

            <small class="events-create-help">
              Define quantas pessoas podem participar neste evento.
            </small>

            @error('max_participants')
              <small class="events-create-error">{{ $message }}</small>
            @enderror
          </div>

          <div class="events-create-field">
            <label for="location_text">
              Localização
            </label>

            <input
              type="text"
              id="location_text"
              name="location_text"
              class="events-create-input @error('location_text') is-invalid @enderror"
              value="{{ old('location_text') }}"
              maxlength="180"
              required
              placeholder="Ex: Praia da Nazaré"
              autocomplete="off"
            >

            <small class="events-create-help">
              Podes escrever o local manualmente ou pesquisar no mapa para preencher automaticamente.
            </small>

            @error('location_text')
              <small class="events-create-error">{{ $message }}</small>
            @enderror
          </div>

          <div class="events-create-field">
            <label for="mapSearch">
              Local no mapa
            </label>

            <div class="events-create-mapbar">
              <input
                type="text"
                id="mapSearch"
                class="events-create-input events-create-mapsearch"
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
            </div>

            <div
              id="eventMap"
              class="events-create-map"
              data-lat="{{ old('lat') }}"
              data-lng="{{ old('lng') }}"
              aria-label="Mapa para escolher a localização do evento"
            ></div>

            <small class="events-create-help">
              Clica no mapa para marcar a localização exata do evento.
            </small>

            @error('lat')
              <small class="events-create-error">{{ $message }}</small>
            @enderror

            @error('lng')
              <small class="events-create-error">{{ $message }}</small>
            @enderror
          </div>

          <input
            type="hidden"
            name="lat"
            id="lat"
            value="{{ old('lat') }}"
          >

          <input
            type="hidden"
            name="lng"
            id="lng"
            value="{{ old('lng') }}"
          >

          <div class="events-create-actions">
            <a class="wj-btn wj-btn-ghost" href="{{ route('events.index') }}">
              <i class="bi bi-x-lg" aria-hidden="true"></i>
              <span>Cancelar</span>
            </a>

            <button type="submit" class="wj-btn wj-btn-primary">
              <i class="bi bi-check2-circle" aria-hidden="true"></i>
              <span>Criar evento</span>
            </button>
          </div>
        </form>
      </section>

      <aside class="events-create-card events-create-card--side" aria-labelledby="event-create-summary-title">
        <div class="events-create-side-icon" aria-hidden="true">
          <i class="bi bi-calendar2-event"></i>
        </div>

        <h2 id="event-create-summary-title" class="events-create-card__title">
          Resumo
        </h2>

        <p class="events-create-card__subtitle">
          Confirma os dados principais antes de criares o evento.
        </p>

        <div class="events-create-summary">
          <div class="events-create-summary__item">
            <span>
              <i class="bi bi-calendar-event" aria-hidden="true"></i>
              Data
            </span>

            <strong id="summaryDate">
              —
            </strong>
          </div>

          <div class="events-create-summary__item">
            <span>
              <i class="bi bi-clock" aria-hidden="true"></i>
              Hora
            </span>

            <strong id="summaryTime">
              —
            </strong>
          </div>

          <div class="events-create-summary__item">
            <span>
              <i class="bi bi-people" aria-hidden="true"></i>
              Participantes
            </span>

            <strong id="summaryParticipants">
              {{ old('max_participants', 10) }}
            </strong>
          </div>

          <div class="events-create-summary__item">
            <span>
              <i class="bi bi-geo-alt" aria-hidden="true"></i>
              Local
            </span>

            <strong id="summaryLocation">
              —
            </strong>
          </div>
        </div>

        <p class="events-create-side-note">
          <i class="bi bi-info-circle" aria-hidden="true"></i>
          Depois de criado, o evento ficará disponível para outros utilizadores consultarem e participarem.
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

  <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
  <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/pt.js"></script>

  @vite('resources/js/events-create.js')

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const dateInput = document.getElementById('event_date');
      const timeInput = document.getElementById('event_time');

      if (dateInput && typeof flatpickr !== 'undefined') {
        flatpickr(dateInput, {
          locale: 'pt',
          dateFormat: 'Y-m-d',
          minDate: dateInput.dataset.min || 'today',
          allowInput: true,
        });
      }

      if (timeInput && typeof flatpickr !== 'undefined') {
        flatpickr(timeInput, {
          locale: 'pt',
          enableTime: true,
          noCalendar: true,
          dateFormat: 'H:i',
          time_24hr: true,
          allowInput: true,
        });
      }

      const summaryDate = document.getElementById('summaryDate');
      const summaryTime = document.getElementById('summaryTime');
      const summaryParticipants = document.getElementById('summaryParticipants');
      const summaryLocation = document.getElementById('summaryLocation');

      const participantsInput = document.getElementById('max_participants');
      const locationInput = document.getElementById('location_text');

      const syncSummary = () => {
        if (summaryDate && dateInput) {
          summaryDate.textContent = dateInput.value || '—';
        }

        if (summaryTime && timeInput) {
          summaryTime.textContent = timeInput.value || '—';
        }

        if (summaryParticipants && participantsInput) {
          summaryParticipants.textContent = participantsInput.value || '—';
        }

        if (summaryLocation && locationInput) {
          summaryLocation.textContent = locationInput.value || '—';
        }
      };

      [dateInput, timeInput, participantsInput, locationInput].forEach((input) => {
        if (input) {
          input.addEventListener('input', syncSummary);
          input.addEventListener('change', syncSummary);
        }
      });

      syncSummary();
    });
  </script>
@endpush