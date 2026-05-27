@extends('layouts.app')

@section('title', 'Pins — Wander Jar')
@section('page-id', 'pins.index')

@push('styles')
  @vite('resources/css/pins-index.css')
@endpush

@section('content')

@php
  $scope = request('scope', '');
  $hasFilters = request('q') || request('scope');
  $pinsCount = method_exists($pins, 'total') ? $pins->total() : count($pins);
@endphp

<main class="pins-page">
  <div class="pins-container">

    <header class="pins-head">
      <div class="pins-head__left">
        <p class="pins-kicker">
          Pins
        </p>

        <h1 class="pins-title">
          Os meus pins
        </h1>

        <p class="pins-subtitle">
          Consulta, pesquisa e gere os teus pins pessoais e os pins associados aos teus grupos.
        </p>

        <div class="pins-head__stats" aria-label="Resumo dos pins">
          <span class="pins-pill">
            <i class="bi bi-pin-map" aria-hidden="true"></i>
            <strong>{{ $pinsCount }}</strong>
            <span>{{ $pinsCount === 1 ? 'pin' : 'pins' }}</span>
          </span>

          @if($hasFilters)
            <span class="pins-pill pins-pill--soft">
              <i class="bi bi-funnel" aria-hidden="true"></i>
              <span>Filtros ativos</span>
            </span>
          @endif
        </div>
      </div>

      <div class="pins-head__right">
        <a class="pins-btn pins-btn--primary" href="{{ route('pins.create') }}">
          <i class="bi bi-plus-lg" aria-hidden="true"></i>
          <span>Criar pin</span>
        </a>

        <a class="pins-btn pins-btn--secondary" href="{{ route('map') }}">
          <i class="bi bi-map" aria-hidden="true"></i>
          <span>Ver mapa</span>
        </a>
      </div>
    </header>

    <section class="pins-toolbar" aria-label="Filtros de pesquisa">
      <form class="pins-filters" method="GET" action="{{ route('pins.index') }}" role="search">

        <div class="pins-field">
          <i class="bi bi-search" aria-hidden="true"></i>

          <input
            type="text"
            name="q"
            placeholder="Pesquisar por título ou local..."
            value="{{ request('q') }}"
            autocomplete="off"
            aria-label="Pesquisar pins por título ou local"
          >
        </div>

        <div class="pins-field pins-field--select pins-select" data-wj-select>
          <i class="bi bi-collection" aria-hidden="true"></i>

          <input
            type="hidden"
            name="scope"
            value="{{ $scope }}"
            data-wj-select-value
          >

          <button
            type="button"
            class="pins-select__btn"
            aria-haspopup="listbox"
            aria-expanded="false"
            data-wj-select-btn
          >
            <span class="pins-select__text" data-wj-select-label>
              @if($scope === 'pessoal')
                Pins pessoais
              @elseif($scope === 'grupo')
                Pins de grupo
              @else
                Todos
              @endif
            </span>

            <i class="bi bi-chevron-down pins-select__chev" aria-hidden="true"></i>
          </button>

          <div
            class="pins-select__menu"
            role="listbox"
            aria-label="Filtrar pins por tipo"
            data-wj-select-menu
          >
            <button
              type="button"
              class="pins-select__opt"
              role="option"
              data-value=""
              data-label="Todos"
              aria-selected="{{ $scope === '' ? 'true' : 'false' }}"
            >
              Todos
            </button>

            <button
              type="button"
              class="pins-select__opt"
              role="option"
              data-value="pessoal"
              data-label="Pins pessoais"
              aria-selected="{{ $scope === 'pessoal' ? 'true' : 'false' }}"
            >
              Pins pessoais
            </button>

            <button
              type="button"
              class="pins-select__opt"
              role="option"
              data-value="grupo"
              data-label="Pins de grupo"
              aria-selected="{{ $scope === 'grupo' ? 'true' : 'false' }}"
            >
              Pins de grupo
            </button>
          </div>
        </div>

        <div class="pins-actions">
          <button class="pins-btn pins-btn--secondary" type="submit">
            <i class="bi bi-funnel" aria-hidden="true"></i>
            <span>Filtrar</span>
          </button>

          @if($hasFilters)
            <a class="pins-btn pins-btn--ghost" href="{{ route('pins.index') }}">
              <i class="bi bi-x-lg" aria-hidden="true"></i>
              <span>Limpar</span>
            </a>
          @endif
        </div>
      </form>

      @if($hasFilters)
        <div class="pins-chips" aria-label="Filtros ativos">
          @if(request('q'))
            <span class="pins-chip">
              <i class="bi bi-search" aria-hidden="true"></i>
              <span>{{ request('q') }}</span>
            </span>
          @endif

          @if(request('scope'))
            <span class="pins-chip">
              <i class="bi bi-collection" aria-hidden="true"></i>

              <span>
                @if(request('scope') === 'pessoal')
                  Pins pessoais
                @elseif(request('scope') === 'grupo')
                  Pins de grupo
                @else
                  {{ request('scope') }}
                @endif
              </span>
            </span>
          @endif
        </div>
      @endif
    </section>

    <section class="pins-grid" data-pins-list aria-label="Lista de pins">
      @forelse($pins as $pin)
        <a class="pins-card" href="{{ route('pins.show', $pin) }}">
          <div class="pins-card__top">
            <div class="pins-card__icon" aria-hidden="true">
              <i class="bi bi-pin-map-fill"></i>
            </div>

            @if(!empty($pin->group_id))
              <span class="pins-badge">
                <i class="bi bi-people" aria-hidden="true"></i>
                <span>Grupo</span>
              </span>
            @else
              <span class="pins-badge pins-badge--soft">
                <i class="bi bi-person" aria-hidden="true"></i>
                <span>Pessoal</span>
              </span>
            @endif
          </div>

          <div class="pins-card__body">
            <h2 class="pins-card__title">
              {{ $pin->title ?? 'Pin' }}
            </h2>

            <p class="pins-card__location">
              <i class="bi bi-geo-alt" aria-hidden="true"></i>
              <span>{{ $pin->location_text ?? 'Sem localização' }}</span>
            </p>
          </div>

          <div class="pins-card__meta">
            <span>
              <i class="bi bi-calendar-event" aria-hidden="true"></i>
              {{ optional($pin->created_at)->format('d/m/Y') ?? 'Data indisponível' }}
            </span>

            @if(!empty($pin->group?->name))
              <span class="pins-dot" aria-hidden="true">•</span>

              <span>
                <i class="bi bi-people" aria-hidden="true"></i>
                {{ $pin->group->name }}
              </span>
            @endif
          </div>

          <div class="pins-card__hint" aria-hidden="true">
            <span>Ver detalhes</span>
            <i class="bi bi-arrow-up-right"></i>
          </div>
        </a>
      @empty
        <div class="pins-empty">
          <div class="pins-empty__icon" aria-hidden="true">
            <i class="bi bi-pin-angle"></i>
          </div>

          <h2 class="pins-empty__title">
            Ainda não existem pins.
          </h2>

          <p class="pins-empty__text">
            Cria o teu primeiro pin e começa a marcar lugares, ideias e memórias no mapa.
          </p>

          <a class="pins-btn pins-btn--primary" href="{{ route('pins.create') }}">
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
            <span>Criar pin</span>
          </a>
        </div>
      @endforelse
    </section>

    @if(method_exists($pins, 'links') && $pins->hasPages())
      <div class="pins-pagination">
        {{ $pins->withQueryString()->links() }}
      </div>
    @endif

  </div>
</main>

@endsection

@push('scripts')
  @vite('resources/js/pins-index.js')
@endpush