@extends('layouts.app')

@section('title', ($pin->title ?? 'Pin') . ' — Wander Jar')
@section('page-id', 'pins.show')

@push('styles')
  @vite('resources/css/pins-show.css')

  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
  />
@endpush

@section('content')

@php
  $imageUrl = $pin->image_url ?? null;

  if (!$imageUrl && !empty($pin->image_path)) {
      $imageUrl = str_starts_with($pin->image_path, '/storage/')
          ? $pin->image_path
          : \Illuminate\Support\Facades\Storage::disk('public')->url($pin->image_path);
  }

  $hasCoordinates = !is_null($pin->lat) && !is_null($pin->lng);
@endphp

<main class="pins-show-page">
  <div class="pins-show-container">

    <header class="pins-show-head">
      <div class="pins-show-head__left">
        <p class="pins-show-kicker">
          <i class="bi bi-pin-map" aria-hidden="true"></i>

          @if($pin->group)
            <span>Pin de grupo</span>
          @else
            <span>Pin pessoal</span>
          @endif
        </p>

        <h1 class="pins-show-title">
          {{ $pin->title ?? 'Sem título' }}
        </h1>

        <p class="pins-show-subtitle">
          <i class="bi bi-geo-alt" aria-hidden="true"></i>
          <span>{{ $pin->location_text ?? 'Sem localização definida' }}</span>

          @if($pin->group)
            <span class="pins-show-dot" aria-hidden="true">•</span>

            <span>
              <strong>Grupo:</strong> {{ $pin->group->name }}
            </span>
          @endif
        </p>
      </div>

      <div class="pins-show-head__right">
        <a class="pins-show-btn pins-show-btn--primary" href="{{ route('pins.edit', $pin) }}">
          <i class="bi bi-pencil-square" aria-hidden="true"></i>
          <span>Editar</span>
        </a>

        <a class="pins-show-btn pins-show-btn--secondary" href="{{ route('pins.index') }}">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
          <span>Voltar</span>
        </a>
      </div>
    </header>

    <article class="pins-show-card">

      <section class="pins-show-image-wrap" aria-label="Imagem do pin">
        @if($imageUrl)
          <img
            src="{{ $imageUrl }}"
            alt="Imagem do pin {{ $pin->title ?? 'sem título' }}"
            class="pins-show-image"
            id="pinsShowImage"
            loading="lazy"
            decoding="async"
            onerror="this.style.display='none'; document.getElementById('pinsShowImageFallback').style.display='flex';"
          >

          <div class="pins-show-image-fallback" id="pinsShowImageFallback" style="display:none;">
            <div class="pins-show-image-fallback__icon" aria-hidden="true">
              <i class="bi bi-image"></i>
            </div>

            <div>
              <h2 class="pins-show-image-fallback__text">
                Não foi possível carregar a imagem deste pin.
              </h2>

              <p class="pins-show-image-fallback__sub">
                A imagem pode ter sido removida ou o ficheiro pode já não estar disponível.
              </p>
            </div>
          </div>
        @else
          <div class="pins-show-image-fallback" style="display:flex;">
            <div class="pins-show-image-fallback__icon" aria-hidden="true">
              <i class="bi bi-image"></i>
            </div>

            <div>
              <h2 class="pins-show-image-fallback__text">
                Este pin ainda não tem imagem.
              </h2>

              <p class="pins-show-image-fallback__sub">
                Podes adicionar uma imagem ao editar este pin.
              </p>
            </div>
          </div>
        @endif
      </section>

      <div class="pins-show-content">

        <section class="pins-show-section" aria-labelledby="pin-description-title">
          <div class="pins-show-section__head">
            <p class="pins-show-section__kicker">
              Memória
            </p>

            <h2 id="pin-description-title" class="pins-show-section__title">
              Descrição
            </h2>
          </div>

          <div class="pins-show-text">
            {!! nl2br(e($pin->content ?: 'Sem descrição.')) !!}
          </div>
        </section>

        @if($hasCoordinates)
          <section class="pins-show-section" aria-labelledby="pin-location-title">
            <div class="pins-show-map-head">
              <div>
                <p class="pins-show-section__kicker">
                  Mapa
                </p>

                <h2 id="pin-location-title" class="pins-show-section__title">
                  Localização
                </h2>
              </div>

              <div class="pins-show-meta__item pins-show-meta__item--compact">
                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                <span>{{ $pin->lat }}, {{ $pin->lng }}</span>
              </div>
            </div>

            <div
              id="pinShowMap"
              class="pins-show-map"
              data-lat="{{ $pin->lat }}"
              data-lng="{{ $pin->lng }}"
              data-title="{{ $pin->title ?? 'Pin' }}"
              data-location="{{ $pin->location_text ?? '' }}"
              aria-label="Mapa com a localização do pin"
            ></div>
          </section>
        @elseif(!empty($pin->location_text))
          <section class="pins-show-section" aria-labelledby="pin-location-title">
            <div class="pins-show-section__head">
              <p class="pins-show-section__kicker">
                Local
              </p>

              <h2 id="pin-location-title" class="pins-show-section__title">
                Localização
              </h2>
            </div>

            <div class="pins-show-meta">
              <div class="pins-show-meta__item">
                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                <span>{{ $pin->location_text }}</span>
              </div>
            </div>
          </section>
        @endif

        <section class="pins-show-section pins-show-section--meta" aria-label="Informação adicional do pin">
          <div class="pins-show-section__head">
            <p class="pins-show-section__kicker">
              Detalhes
            </p>

            <h2 class="pins-show-section__title">
              Informação adicional
            </h2>
          </div>

          <div class="pins-show-meta">
            <div class="pins-show-meta__item">
              <i class="bi bi-calendar-event" aria-hidden="true"></i>

              <span>
                Criado em {{ optional($pin->created_at)->format('d/m/Y') ?? 'data indisponível' }}
              </span>
            </div>

            @if($pin->updated_at && $pin->created_at && $pin->updated_at->ne($pin->created_at))
              <div class="pins-show-meta__item">
                <i class="bi bi-clock-history" aria-hidden="true"></i>

                <span>
                  Atualizado em {{ optional($pin->updated_at)->format('d/m/Y') }}
                </span>
              </div>
            @endif

            @if($pin->group)
              <div class="pins-show-meta__item">
                <i class="bi bi-people" aria-hidden="true"></i>

                <span>
                  Associado ao grupo {{ $pin->group->name }}
                </span>
              </div>
            @else
              <div class="pins-show-meta__item">
                <i class="bi bi-person" aria-hidden="true"></i>

                <span>
                  Pin pessoal
                </span>
              </div>
            @endif
          </div>
        </section>

      </div>
    </article>

  </div>
</main>

@endsection

@push('scripts')
  <script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""
  ></script>

  @vite('resources/js/pins-show.js')
@endpush