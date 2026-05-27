@extends('layouts.app')

@section('title', 'Mapa pessoal — Wander Jar')
@section('page-id', 'map.index')

@push('styles')
  @vite('resources/css/map.css')

  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    crossorigin=""
  />
@endpush

@section('content')
@php
  $personalPins = ($pins ?? collect())
    ->filter(fn ($pin) => empty($pin->group_id))
    ->values();

  $pinsWithCoords = $personalPins
    ->filter(fn ($pin) => is_numeric($pin->lat) && is_numeric($pin->lng))
    ->values();

  $hasPins = $pinsWithCoords->isNotEmpty();

  $pinsData = $pinsWithCoords->map(function ($pin) {
    return [
      'id' => $pin->id,
      'title' => $pin->title ?? 'Sem título',
      'content' => $pin->content,
      'location_text' => $pin->location_text,
      'lat' => (float) $pin->lat,
      'lng' => (float) $pin->lng,
      'image_url' => $pin->image_url ?? null,
      'url' => route('pins.show', $pin),
    ];
  })->all();
@endphp

<main class="map-page">
  <div class="container-xl">

    <section class="map-hero" aria-labelledby="personal-map-title">
      <div class="map-hero__left">
        <div class="map-hero__badge" aria-hidden="true">
          <i class="bi bi-pin-map-fill"></i>
        </div>

        <div class="map-hero__txt">
          <p class="map-hero__kicker">
            Mapa
          </p>

          <h1 id="personal-map-title" class="map-title">
            Mapa pessoal
          </h1>

          <p class="map-subtitle">
            Explora todos os teus pins pessoais guardados diretamente no mapa.
          </p>

          <div class="map-hero__chips" aria-label="Características do mapa pessoal">
            <span class="wj-chip">
              <i class="bi bi-map" aria-hidden="true"></i>
              <span>Leaflet</span>
            </span>

            <span class="wj-chip">
              <i class="bi bi-globe2" aria-hidden="true"></i>
              <span>OpenStreetMap</span>
            </span>

            <span class="wj-chip">
              <i class="bi bi-person" aria-hidden="true"></i>
              <span>Pessoal</span>
            </span>
          </div>
        </div>
      </div>

      <div class="map-actions">
        <a class="wj-btn wj-btn--primary" href="{{ route('pins.create') }}">
          <i class="bi bi-plus-lg" aria-hidden="true"></i>
          <span>Novo pin</span>
        </a>

        <button class="wj-btn wj-btn--ghost" type="button" id="locateBtn">
          <i class="bi bi-geo-alt" aria-hidden="true"></i>
          <span>Localização atual</span>
        </button>
      </div>
    </section>

    <section class="map-shell" aria-label="Mapa pessoal com pins">

      <div class="map-card">
        <div
          id="personalMap"
          class="wj-map"
          aria-label="Mapa com os teus pins pessoais"
        ></div>

        <div
          id="mapHint"
          class="map-emptyOverlay"
          style="display: {{ $hasPins ? 'none' : 'flex' }}"
        >
          <div class="map-emptyOverlay__card">
            <div class="map-emptyOverlay__icon" aria-hidden="true">
              <i class="bi bi-pin-map"></i>
            </div>

            <h2 class="map-emptyOverlay__title">
              O teu mapa ainda está vazio
            </h2>

            <p class="map-emptyOverlay__text">
              Cria um pin e escolhe um ponto no mapa para começares a construir o teu mapa pessoal.
            </p>

            <div class="map-emptyOverlay__actions">
              <a class="wj-btn wj-btn--primary wj-btn--sm" href="{{ route('pins.create') }}">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                <span>Criar pin</span>
              </a>

              <span class="map-emptyOverlay__hint">
                Dica: começa por um lugar especial.
              </span>
            </div>
          </div>
        </div>
      </div>

      <aside class="map-side" aria-labelledby="personal-map-pins-title">
        <div class="map-side__head">
          <div>
            <h2 id="personal-map-pins-title" class="map-side__title">
              Pins pessoais
            </h2>

            <p class="map-side__subtitle">
              Clica num pin para navegar até ao local no mapa.
            </p>
          </div>

          <div class="map-side__meta">
            <span class="map-count" id="pinsCount">
              {{ $pinsWithCoords->count() }}
            </span>
          </div>
        </div>

        <div class="map-side__scroll">
          @if(!$hasPins)
            <div class="map-empty">
              <div class="map-empty__top">
                <div class="map-empty__icon" aria-hidden="true">
                  <i class="bi bi-map"></i>
                </div>

                <div>
                  <h3 class="map-empty__title">
                    Sem pins com localização
                  </h3>

                  <p class="map-empty__text">
                    Os pins aparecem aqui quando tiverem latitude e longitude associadas.
                  </p>
                </div>
              </div>

              <a class="wj-btn wj-btn--primary wj-btn--block" href="{{ route('pins.create') }}">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                <span>Criar pin</span>
              </a>

              <p class="map-empty__mini">
                Quando tiveres pins, podes clicar na lista para ir diretamente até ao local.
              </p>
            </div>
          @else
            <ul class="map-list" id="pinsList">
              @foreach($pinsWithCoords as $pin)
                <li class="map-list__item" data-pin-id="{{ $pin->id }}">
                  <button
                    class="map-list__btn"
                    type="button"
                    data-lat="{{ $pin->lat }}"
                    data-lng="{{ $pin->lng }}"
                    data-pin-id="{{ $pin->id }}"
                  >
                    <span class="map-dot" aria-hidden="true"></span>

                    <span class="map-list__txt">
                      <span class="map-list__title">
                        {{ $pin->title ?? 'Sem título' }}
                      </span>

                      <span class="map-list__sub">
                        {{ $pin->location_text ?? 'Localização não definida' }}
                      </span>
                    </span>

                    <span class="map-list__chev" aria-hidden="true">
                      ›
                    </span>
                  </button>
                </li>
              @endforeach
            </ul>
          @endif
        </div>
      </aside>

    </section>

  </div>
</main>

<script id="pinsData" type="application/json">
  @json($pinsData, JSON_UNESCAPED_SLASHES)
</script>
@endsection

@push('scripts')
  <meta name="maptiler-key" content="{{ env('MAPTILER_KEY') }}">

  <script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    crossorigin=""
  ></script>

  @vite('resources/js/map.js')
@endpush