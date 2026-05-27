@extends('layouts.app')

@section('title', 'Grupos — Wander Jar')
@section('page-id', 'groups.index')

@push('styles')
  @vite('resources/css/groups.css')
@endpush

@section('content')

@php
  $groupsCol = collect($groups ?? []);

  $total = $groupsCol->count();
  $hasGroups = $total > 0;

  $admins = $groupsCol->filter(fn($group) => (($group->pivot->role ?? 'member') === 'admin'))->count();
  $members = $groupsCol->filter(fn($group) => (($group->pivot->role ?? 'member') === 'member'))->count();
  $pinsTotal = (int) $groupsCol->sum(fn($group) => $group->pins_count ?? 0);
@endphp

@if (session('status'))
  <script>
    window.__FLASH_STATUS__ = @json(session('status'));
  </script>
@endif

<main class="gr-page">
  <div class="gr-container">

    <header class="gr-head">
      <div class="gr-left">
        <div class="gr-badge" aria-hidden="true">
          <i class="bi bi-people-fill"></i>
        </div>

        <div class="gr-titles">
          <p class="gr-kicker">
            Grupos
          </p>

          <h1 class="gr-title">
            Os meus grupos
          </h1>

          <p class="gr-subtitle">
            Gere os teus grupos, consulta os membros e acede aos mapas partilhados de cada comunidade.
          </p>

          <div class="gr-pills" aria-label="Funcionalidades dos grupos">
            <span class="gr-pill">
              <i class="bi bi-link-45deg" aria-hidden="true"></i>
              <span>Entrada por convite</span>
            </span>

            <span class="gr-pill gr-pill--soft">
              <i class="bi bi-person-check" aria-hidden="true"></i>
              <span>Gestão de membros</span>
            </span>

            <span class="gr-pill gr-pill--soft">
              <i class="bi bi-pin-map" aria-hidden="true"></i>
              <span>Mapas partilhados</span>
            </span>
          </div>
        </div>
      </div>

      <div class="gr-actions">
        <a class="gr-btn gr-btn--ghost" href="{{ route('dashboard') }}">
          <i class="bi bi-house" aria-hidden="true"></i>
          <span>Início</span>
        </a>

        <a class="gr-btn gr-btn--primary" href="{{ route('groups.create') }}">
          <i class="bi bi-plus-circle" aria-hidden="true"></i>
          <span>Criar grupo</span>
        </a>
      </div>
    </header>

    <section class="gr-toolbar" aria-label="Ferramentas de pesquisa e filtro">
      <div class="gr-search">
        <i class="bi bi-search" aria-hidden="true"></i>

        <input
          id="grSearch"
          type="text"
          placeholder="Pesquisar grupos..."
          autocomplete="off"
          aria-label="Pesquisar grupos pelo nome"
        >

        <button
          class="gr-clear"
          id="grClear"
          type="button"
          aria-label="Limpar pesquisa"
          hidden
        >
          <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
      </div>

      <div class="gr-filters" role="group" aria-label="Filtros de grupos">
        <button class="gr-chip is-active" type="button" data-filter="all">
          <i class="bi bi-stars" aria-hidden="true"></i>
          <span>Todos</span>
        </button>

        <button class="gr-chip" type="button" data-filter="admin">
          <i class="bi bi-person-badge" aria-hidden="true"></i>
          <span>Admin</span>
        </button>

        <button class="gr-chip" type="button" data-filter="member">
          <i class="bi bi-person-check" aria-hidden="true"></i>
          <span>Membro</span>
        </button>
      </div>

      <div class="gr-rightTools">
        <button
          class="gr-iconbtn"
          id="grLayoutBtn"
          type="button"
          title="Alternar layout"
          aria-label="Alternar layout"
        >
          <i class="bi bi-grid-3x3-gap" aria-hidden="true"></i>
        </button>

        <button
          class="gr-iconbtn"
          id="grSortBtn"
          type="button"
          title="Ordenar grupos"
          aria-label="Ordenar grupos"
        >
          <i class="bi bi-sort-down" aria-hidden="true"></i>
        </button>
      </div>
    </section>

    <section class="gr-stats" aria-label="Resumo dos grupos">
      <article class="gr-stat">
        <div class="gr-stat__ic" aria-hidden="true">
          <i class="bi bi-people"></i>
        </div>

        <div class="gr-stat__txt">
          <b id="grTotal">{{ $total }}</b>
          <span>Total</span>
        </div>
      </article>

      <article class="gr-stat">
        <div class="gr-stat__ic gr-stat__ic--alt" aria-hidden="true">
          <i class="bi bi-person-badge"></i>
        </div>

        <div class="gr-stat__txt">
          <b id="grAdmins">{{ $admins }}</b>
          <span>Como admin</span>
        </div>
      </article>

      <article class="gr-stat">
        <div class="gr-stat__ic gr-stat__ic--soft" aria-hidden="true">
          <i class="bi bi-person-check"></i>
        </div>

        <div class="gr-stat__txt">
          <b id="grMember">{{ $members }}</b>
          <span>Como membro</span>
        </div>
      </article>

      <article class="gr-stat">
        <div class="gr-stat__ic gr-stat__ic--vio" aria-hidden="true">
          <i class="bi bi-geo-alt-fill"></i>
        </div>

        <div class="gr-stat__txt">
          <b id="grPins">{{ $pinsTotal }}</b>
          <span>Pins</span>
        </div>
      </article>
    </section>

    <section class="gr-gridWrap" aria-label="Lista de grupos">
      @if(!$hasGroups)
        <div class="gr-empty">
          <div class="gr-empty__ic" aria-hidden="true">
            <i class="bi bi-people"></i>
          </div>

          <h2 class="gr-empty__title">
            Ainda não tens grupos
          </h2>

          <p class="gr-empty__text">
            Cria o teu primeiro grupo para começares a organizar mapas e memórias em conjunto.
          </p>

          <a class="gr-btn gr-btn--primary" href="{{ route('groups.create') }}">
            <i class="bi bi-plus-circle" aria-hidden="true"></i>
            <span>Criar grupo</span>
          </a>
        </div>
      @else
        <div class="gr-grid" id="grGrid">
          @foreach($groupsCol as $group)
            @php
              $role = $group->pivot->role ?? 'member';
              $isAdmin = $role === 'admin';
              $pinsCount = $group->pins_count ?? 0;
              $membersCount = $group->users_count ?? ($group->relationLoaded('users') ? $group->users->count() : 0);
              $createdDate = optional($group->created_at)->format('d/m/Y');
              $inviteUrl = !empty($group->invite_code) ? url('/groups/join/' . $group->invite_code) : null;
            @endphp

            <article
              class="gr-card"
              data-name="{{ \Illuminate\Support\Str::lower($group->name) }}"
              data-role="{{ $role }}"
              data-created="{{ optional($group->created_at)->timestamp ?? 0 }}"
              data-pins="{{ $pinsCount }}"
            >
              <div class="gr-card__top">
                <div class="gr-card__icon" aria-hidden="true">
                  <i class="bi bi-people-fill"></i>
                </div>

                <span class="gr-role {{ $isAdmin ? 'is-admin' : 'is-member' }}">
                  <i class="bi {{ $isAdmin ? 'bi-shield-fill-check' : 'bi-person-fill' }}" aria-hidden="true"></i>
                  <span>{{ $isAdmin ? 'Admin' : 'Membro' }}</span>
                </span>
              </div>

              <div class="gr-card__body">
                <h2 class="gr-card__title">
                  {{ $group->name }}
                </h2>

                <p class="gr-card__desc">
                  {{ $group->description ?: 'Sem descrição definida.' }}
                </p>
              </div>

              <div class="gr-card__meta">
                <span>
                  <i class="bi bi-pin-map" aria-hidden="true"></i>
                  {{ $pinsCount }} {{ $pinsCount === 1 ? 'pin' : 'pins' }}
                </span>

                <span class="gr-dot" aria-hidden="true">•</span>

                <span>
                  <i class="bi bi-people" aria-hidden="true"></i>
                  {{ $membersCount }} {{ $membersCount === 1 ? 'membro' : 'membros' }}
                </span>

                @if($createdDate)
                  <span class="gr-dot" aria-hidden="true">•</span>

                  <span>
                    <i class="bi bi-calendar3" aria-hidden="true"></i>
                    {{ $createdDate }}
                  </span>
                @endif
              </div>

              <div class="gr-card__actions">
                <a class="gr-cardBtn gr-cardBtn--primary" href="{{ route('groups.map', $group) }}">
                  <i class="bi bi-map" aria-hidden="true"></i>
                  <span>Mapa</span>
                </a>

                <a class="gr-cardBtn gr-cardBtn--secondary" href="{{ route('groups.chat', $group) }}">
                  <i class="bi bi-chat-dots" aria-hidden="true"></i>
                  <span>Chat</span>
                </a>

                <a class="gr-cardBtn gr-cardBtn--secondary" href="{{ route('groups.members', $group) }}">
                  <i class="bi bi-people" aria-hidden="true"></i>
                  <span>Membros</span>
                </a>

                @if($isAdmin)
                  <a class="gr-cardBtn gr-cardBtn--ghost" href="{{ route('groups.edit', $group) }}">
                    <i class="bi bi-pencil-square" aria-hidden="true"></i>
                    <span>Editar</span>
                  </a>
                @endif
              </div>

              @if($inviteUrl)
                <button
                  type="button"
                  class="gr-invite"
                  data-invite="{{ $inviteUrl }}"
                  data-group="{{ $group->name }}"
                >
                  <i class="bi bi-link-45deg" aria-hidden="true"></i>
                  <span>Copiar convite</span>
                </button>
              @endif
            </article>
          @endforeach
        </div>

        <div class="gr-noResults" id="grNoResults" hidden>
          <div class="gr-empty__ic" aria-hidden="true">
            <i class="bi bi-search"></i>
          </div>

          <h2 class="gr-empty__title">
            Nenhum grupo encontrado
          </h2>

          <p class="gr-empty__text">
            Experimenta alterar a pesquisa ou limpar os filtros ativos.
          </p>
        </div>
      @endif
    </section>

  </div>
</main>

<div class="gr-toast" id="grToast" role="status" aria-live="polite" hidden>
  <i class="bi bi-check-circle" aria-hidden="true"></i>
  <span id="grToastText">Copiado.</span>
</div>

@endsection

@push('scripts')
  @vite('resources/js/groups.js')
@endpush