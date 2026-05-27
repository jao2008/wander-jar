@extends('layouts.app')

@section('title', 'Criar grupo — Wander Jar')
@section('page-id', 'groups.create')

@push('styles')
  @vite('resources/css/groups-create.css')
@endpush

@section('content')

<main class="gc-page">
  <div class="gc-container">

    <header class="gc-head">
      <div class="gc-left">
        <div class="gc-badge" aria-hidden="true">
          <i class="bi bi-plus-circle-fill"></i>
        </div>

        <div class="gc-titles">
          <p class="gc-kicker">
            Criar grupo
          </p>

          <h1 class="gc-title">
            Novo grupo
          </h1>

          <p class="gc-subtitle">
            Cria um espaço privado para partilhar pins, mapas e memórias com outras pessoas através de convite.
          </p>

          <div class="gc-pills" aria-label="Características do grupo">
            <span class="gc-pill">
              <i class="bi bi-shield-lock" aria-hidden="true"></i>
              <span>Só por convite</span>
            </span>

            <span class="gc-pill gc-pill--soft">
              <i class="bi bi-people" aria-hidden="true"></i>
              <span>Partilhado</span>
            </span>

            <span class="gc-pill gc-pill--soft">
              <i class="bi bi-pin-map" aria-hidden="true"></i>
              <span>Mapa do grupo</span>
            </span>
          </div>
        </div>
      </div>

      <div class="gc-actions">
        <a class="gc-btn gc-btn--ghost" href="{{ route('groups.index') }}">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
          <span>Os meus grupos</span>
        </a>

        <a class="gc-btn gc-btn--ghost" href="{{ route('dashboard') }}">
          <i class="bi bi-house" aria-hidden="true"></i>
          <span>Início</span>
        </a>
      </div>
    </header>

    @if ($errors->any())
      <div class="gc-note gc-note--error" role="alert">
        <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>

        <div>
          <strong>Há erros no formulário:</strong>

          <ul>
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <div class="gc-grid">

      <section class="gc-card" aria-labelledby="group-create-form-title">
        <div class="gc-card__head">
          <div class="gc-card__title">
            <i class="bi bi-pencil-square" aria-hidden="true"></i>

            <div>
              <b id="group-create-form-title">Detalhes do grupo</b>
              <small>Define o nome e a descrição do novo grupo.</small>
            </div>
          </div>

          <div class="gc-miniTag">
            <i class="bi bi-eye" aria-hidden="true"></i>
            <span>Pré-visualização</span>
          </div>
        </div>

        <form id="gcForm" class="gc-form" method="POST" action="{{ route('groups.store') }}">
          @csrf

          <input type="hidden" name="privacy" value="private">
          <input type="hidden" name="map_style" value="classic">

          <div class="gc-field">
            <label class="gc-label" for="groupName">
              Nome do grupo
            </label>

            <div class="gc-input">
              <i class="bi bi-people" aria-hidden="true"></i>

              <input
                id="groupName"
                name="name"
                type="text"
                placeholder="Ex: Viagem de verão"
                maxlength="40"
                required
                autocomplete="off"
                value="{{ old('name') }}"
              >

              <span class="gc-counter" id="nameCounter">
                {{ mb_strlen(old('name', '')) }}/40
              </span>
            </div>

            <p class="gc-hint">
              Escolhe um nome curto e fácil de reconhecer. Máximo de 40 caracteres.
            </p>

            <p class="gc-error" id="errName" hidden>
              Escolhe um nome válido com, pelo menos, 3 caracteres.
            </p>
          </div>

          <div class="gc-field">
            <label class="gc-label" for="groupDesc">
              Descrição
              <span class="gc-label-muted">(opcional)</span>
            </label>

            <div class="gc-textareaWrap">
              <i class="bi bi-chat-left-text" aria-hidden="true"></i>

              <textarea
                id="groupDesc"
                name="description"
                rows="4"
                maxlength="255"
                placeholder="Ex: Lugares para visitar, ideias para planos e memórias partilhadas."
              >{{ old('description') }}</textarea>
            </div>

            <p class="gc-hint">
              Podes usar a descrição para explicar o objetivo do grupo.
            </p>
          </div>

          <div class="gc-sideTips">
            <div class="gc-tip">
              <i class="bi bi-info-circle" aria-hidden="true"></i>

              <div>
                <b>Convites</b>
                <p>Depois de criares o grupo, será gerado um código único para convidares outras pessoas.</p>
              </div>
            </div>

            <div class="gc-tip">
              <i class="bi bi-pin-map" aria-hidden="true"></i>

              <div>
                <b>Mapa do grupo</b>
                <p>Os membros podem guardar e consultar pins partilhados no mapa do grupo.</p>
              </div>
            </div>
          </div>

          <div class="gc-actionsRow">
            <button class="gc-btn gc-btn--primary" type="submit" id="gcSubmit">
              <i class="bi bi-check2-circle" aria-hidden="true"></i>
              <span>Criar grupo</span>
            </button>

            <button class="gc-btn gc-btn--ghost" type="button" id="gcGenerate">
              <i class="bi bi-dice-5" aria-hidden="true"></i>
              <span>Gerar nome</span>
            </button>

            <a class="gc-btn gc-btn--ghost" href="{{ route('groups.index') }}">
              <i class="bi bi-x-circle" aria-hidden="true"></i>
              <span>Cancelar</span>
            </a>
          </div>

          <div class="gc-note">
            <i class="bi bi-info-circle" aria-hidden="true"></i>
            <span>O grupo será privado e só poderá ser acedido por utilizadores convidados.</span>
          </div>
        </form>
      </section>

      <aside class="gc-card gc-card--side" aria-labelledby="group-create-summary-title">
        <div class="gc-sideHead">
          <div class="gc-sideIcon" aria-hidden="true">
            <i class="bi bi-stars"></i>
          </div>

          <div class="gc-sideText">
            <h3 id="group-create-summary-title" class="gc-sideTitle">
              Resumo
            </h3>

            <p class="gc-sideSub">
              Confirma como o grupo vai aparecer antes de o criares.
            </p>
          </div>
        </div>

        <div class="gc-summary">
          <div class="gc-sumItem">
            <span class="gc-sumKey">
              <i class="bi bi-people" aria-hidden="true"></i>
              <span>Nome</span>
            </span>

            <span class="gc-sumVal" id="sumName">
              {{ old('name') ?: '—' }}
            </span>
          </div>

          <div class="gc-sumItem">
            <span class="gc-sumKey">
              <i class="bi bi-chat-left-text" aria-hidden="true"></i>
              <span>Descrição</span>
            </span>

            <span class="gc-sumVal" id="sumDesc">
              {{ old('description') ?: '—' }}
            </span>
          </div>

          <div class="gc-sumItem">
            <span class="gc-sumKey">
              <i class="bi bi-shield-lock" aria-hidden="true"></i>
              <span>Privacidade</span>
            </span>

            <span class="gc-sumVal">
              Privado
            </span>
          </div>

          <div class="gc-sumItem">
            <span class="gc-sumKey">
              <i class="bi bi-link-45deg" aria-hidden="true"></i>
              <span>Convite</span>
            </span>

            <span class="gc-sumVal">
              Gerado automaticamente
            </span>
          </div>
        </div>

        <div class="gc-preview">
          <div class="gc-preview__top">
            <div class="gc-preview__icon" aria-hidden="true">
              <i class="bi bi-people-fill"></i>
            </div>

            <span class="gc-preview__role">
              Admin
            </span>
          </div>

          <h4 id="previewName">
            {{ old('name') ?: 'Nome do grupo' }}
          </h4>

          <p id="previewDesc">
            {{ old('description') ?: 'A descrição do grupo vai aparecer aqui.' }}
          </p>

          <div class="gc-preview__meta">
            <span>
              <i class="bi bi-pin-map" aria-hidden="true"></i>
              0 pins
            </span>

            <span>
              <i class="bi bi-person" aria-hidden="true"></i>
              1 membro
            </span>
          </div>
        </div>
      </aside>

    </div>

  </div>
</main>

@endsection

@push('scripts')
  @vite('resources/js/groups-create.js')
@endpush