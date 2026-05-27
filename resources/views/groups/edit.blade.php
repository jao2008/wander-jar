@extends('layouts.app')

@section('title', 'Editar grupo — Wander Jar')
@section('page-id', 'groups.edit')

@push('styles')
  @vite('resources/css/groups-edit.css')
@endpush

@section('content')

<main class="group-edit-page">
  <div class="group-edit-container">

    <header class="group-edit-hero">
      <div class="group-edit-hero__left">
        <div class="group-edit-badge" aria-hidden="true">
          <i class="bi bi-pencil-square"></i>
        </div>

        <div>
          <p class="group-edit-kicker">
            Gestão do grupo
          </p>

          <h1 class="group-edit-title">
            Editar grupo
          </h1>

          <p class="group-edit-subtitle">
            Atualiza o nome e a descrição do grupo para manteres a informação clara para todos os membros.
          </p>

          <div class="group-edit-chips" aria-label="Resumo rápido do grupo">
            <span class="group-chip">
              <i class="bi bi-shield-check" aria-hidden="true"></i>
              Administrador
            </span>

            <span class="group-chip">
              <i class="bi bi-pin-map" aria-hidden="true"></i>
              {{ $group->pins_count ?? $group->pins()->count() }} pins
            </span>

            <span class="group-chip">
              <i class="bi bi-calendar-event" aria-hidden="true"></i>
              Criado em {{ optional($group->created_at)->format('d/m/Y') }}
            </span>
          </div>
        </div>
      </div>

      <div class="group-edit-hero__actions">
        <a href="{{ route('groups.index') }}" class="group-btn group-btn-secondary">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
          <span>Voltar</span>
        </a>
      </div>
    </header>

    @if ($errors->any())
      <div class="group-alert group-alert-danger" role="alert">
        <div class="group-alert__icon" aria-hidden="true">
          <i class="bi bi-exclamation-triangle"></i>
        </div>

        <div>
          <strong>Há erros no formulário:</strong>

          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <div class="group-edit-grid">

      <section class="group-edit-card" aria-labelledby="group-edit-form-title">
        <div class="group-edit-card__head">
          <div>
            <p class="group-edit-card__kicker">
              Informação principal
            </p>

            <h2 id="group-edit-form-title" class="group-edit-card__title">
              Detalhes do grupo
            </h2>

            <p class="group-edit-card__subtitle">
              Estes dados serão apresentados na lista de grupos, no mapa partilhado e nas áreas associadas ao grupo.
            </p>
          </div>
        </div>

        <form
          method="POST"
          action="{{ route('groups.update', $group) }}"
          class="group-form"
        >
          @csrf
          @method('PATCH')

          <div class="group-field">
            <label for="name">
              Nome do grupo
            </label>

            <div class="group-input-wrap">
              <i class="bi bi-people" aria-hidden="true"></i>

              <input
                type="text"
                id="name"
                name="name"
                class="group-input @error('name') is-invalid @enderror"
                maxlength="120"
                required
                value="{{ old('name', $group->name) }}"
                placeholder="Ex: Viagens de verão"
                autocomplete="off"
              >
            </div>

            @error('name')
              <small class="group-error">
                {{ $message }}
              </small>
            @enderror
          </div>

          <div class="group-field">
            <label for="description">
              Descrição
              <span class="group-label-muted">(opcional)</span>
            </label>

            <textarea
              id="description"
              name="description"
              class="group-input group-textarea @error('description') is-invalid @enderror"
              rows="6"
              maxlength="1000"
              placeholder="Descreve o objetivo do grupo..."
            >{{ old('description', $group->description) }}</textarea>

            <small class="group-help">
              Ajuda os membros a perceberem o objetivo do grupo. Máximo de 1000 caracteres.
            </small>

            @error('description')
              <small class="group-error">
                {{ $message }}
              </small>
            @enderror
          </div>

          <div class="group-actions">
            <a
              href="{{ route('groups.index') }}"
              class="group-btn group-btn-secondary"
            >
              <i class="bi bi-x-lg" aria-hidden="true"></i>
              <span>Cancelar</span>
            </a>

            <button
              type="submit"
              class="group-btn group-btn-primary"
            >
              <i class="bi bi-check2-circle" aria-hidden="true"></i>
              <span>Guardar alterações</span>
            </button>
          </div>
        </form>
      </section>

      <aside class="group-edit-card group-edit-card--side" aria-labelledby="group-edit-summary-title">
        <div class="group-edit-side-icon" aria-hidden="true">
          <i class="bi bi-stars"></i>
        </div>

        <p class="group-edit-card__kicker">
          Resumo
        </p>

        <h2 id="group-edit-summary-title" class="group-edit-card__title">
          {{ $group->name }}
        </h2>

        <p class="group-edit-card__subtitle">
          Confirma os dados principais antes de guardar as alterações.
        </p>

        <div class="group-summary">
          <div class="group-summary__item">
            <span>
              <i class="bi bi-people" aria-hidden="true"></i>
              Nome
            </span>

            <strong>
              {{ $group->name }}
            </strong>
          </div>

          <div class="group-summary__item">
            <span>
              <i class="bi bi-shield-lock" aria-hidden="true"></i>
              Acesso
            </span>

            <strong>
              Por convite
            </strong>
          </div>

          <div class="group-summary__item">
            <span>
              <i class="bi bi-pin-map" aria-hidden="true"></i>
              Pins
            </span>

            <strong>
              {{ $group->pins_count ?? $group->pins()->count() }}
            </strong>
          </div>

          <div class="group-summary__item">
            <span>
              <i class="bi bi-calendar-event" aria-hidden="true"></i>
              Criado em
            </span>

            <strong>
              {{ optional($group->created_at)->format('d/m/Y') }}
            </strong>
          </div>
        </div>

        <div class="group-side-actions">
          <a href="{{ route('groups.map', $group) }}" class="group-mini-btn">
            <i class="bi bi-pin-map" aria-hidden="true"></i>
            <span>Abrir mapa</span>
          </a>

          <a href="{{ route('groups.members', $group) }}" class="group-mini-btn">
            <i class="bi bi-people" aria-hidden="true"></i>
            <span>Gerir membros</span>
          </a>

          @if(Route::has('groups.chat'))
            <a href="{{ route('groups.chat', $group) }}" class="group-mini-btn">
              <i class="bi bi-chat-dots" aria-hidden="true"></i>
              <span>Abrir chat</span>
            </a>
          @endif
        </div>

        <p class="group-side-note">
          <i class="bi bi-info-circle" aria-hidden="true"></i>
          Apenas administradores do grupo podem editar estas informações.
        </p>
      </aside>

    </div>

  </div>
</main>

@endsection