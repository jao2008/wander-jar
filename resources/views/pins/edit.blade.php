@extends('layouts.app')

@section('title', 'Editar pin — Wander Jar')
@section('page-id', 'pins.edit')

@push('styles')
  @vite('resources/css/pins-create.css')

  <link
    rel="stylesheet"
    href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
    crossorigin=""
  />
@endpush

@section('content')

@php
  $groups = collect($groups ?? []);

  $currentImageUrl = $pin->image_url ?? null;

  if (!$currentImageUrl && !empty($pin->image_path)) {
      $imagePath = $pin->image_path;

      if (
          \Illuminate\Support\Str::startsWith($imagePath, ['http://', 'https://', '/storage/'])
      ) {
          $currentImageUrl = $imagePath;
      } else {
          $currentImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url($imagePath);
      }
  }

  $oldGroupId = old('group_id', $pin->group_id ?? '');

  if (is_null($oldGroupId)) {
      $oldGroupId = '';
  }

  $selectedGroupText = 'Pin pessoal';

  if ($oldGroupId !== '') {
      $selectedGroup = $groups->firstWhere('id', (int) $oldGroupId);

      if ($selectedGroup) {
          $selectedGroupText = $selectedGroup->name;
      }
  }

  $oldTitle = old('title', $pin->title);
  $oldContent = old('content', $pin->content);
  $oldLocation = old('location_text', $pin->location_text);
  $oldLat = old('lat', $pin->lat);
  $oldLng = old('lng', $pin->lng);
@endphp

<main class="pins-create-page">
  <div class="pins-create-container">

    <header class="pins-create-head">
      <div class="pins-create-head__left">
        <p class="pins-create-kicker">
          <i class="bi bi-pencil-square" aria-hidden="true"></i>
          <span>Editar pin</span>
        </p>

        <h1 class="pins-create-title">
          Editar pin
        </h1>

        <p class="pins-create-subtitle">
          Atualiza a memória, localização, grupo ou imagem associada a este pin.
        </p>
      </div>

      <div class="pins-create-head__right">
        <a href="{{ route('pins.show', $pin) }}" class="pins-create-btn pins-create-btn--secondary">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
          <span>Voltar ao pin</span>
        </a>
      </div>
    </header>

    @if ($errors->any())
      <div class="pins-create-alert pins-create-alert--danger" role="alert">
        <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>

        <div>
          <strong>Há erros no formulário.</strong>

          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <div class="pins-create-grid">

      <section class="pins-create-card pins-create-card--form" aria-labelledby="pin-edit-form-title">
        <div class="pins-create-card__head">
          <div>
            <p class="pins-create-card__kicker">
              Detalhes
            </p>

            <h2 id="pin-edit-form-title" class="pins-create-card__title">
              Informação do pin
            </h2>

            <p class="pins-create-card__subtitle">
              Edita os dados principais e ajusta a localização no mapa.
            </p>
          </div>
        </div>

        <form
          method="POST"
          action="{{ route('pins.update', $pin) }}"
          id="pinForm"
          enctype="multipart/form-data"
          class="pins-create-form"
        >
          @csrf
          @method('PATCH')

          <div class="pins-create-field">
            <label for="pinTitle">
              Título
            </label>

            <input
              id="pinTitle"
              type="text"
              name="title"
              value="{{ $oldTitle }}"
              maxlength="120"
              required
              class="@error('title') is-invalid @enderror"
              placeholder="Ex: Pôr do sol incrível"
              autocomplete="off"
            >

            @error('title')
              <small class="pins-create-error">
                {{ $message }}
              </small>
            @enderror
          </div>

          <div class="pins-create-field">
            <label for="group_id">
              Grupo
            </label>

            <select
              name="group_id"
              id="group_id"
              class="pins-create-native-select"
              aria-hidden="true"
              tabindex="-1"
            >
              <option value="" @selected($oldGroupId === '')>
                Pin pessoal
              </option>

              @foreach($groups as $group)
                <option
                  value="{{ $group->id }}"
                  @selected((string) $oldGroupId === (string) $group->id)
                >
                  {{ $group->name }}
                </option>
              @endforeach
            </select>

            <div class="pins-create-select" data-select="group">
              <button
                type="button"
                class="pins-create-select__btn"
                data-select-btn="group"
                aria-haspopup="listbox"
                aria-expanded="false"
              >
                <span class="pins-create-select__value" data-select-value="group">
                  {{ $selectedGroupText }}
                </span>

                <i class="bi bi-chevron-down pins-create-select__chev" aria-hidden="true"></i>
              </button>

              <div
                class="pins-create-select__menu"
                data-select-menu="group"
                role="listbox"
                aria-label="Escolher grupo"
              >
                <button
                  type="button"
                  class="pins-create-select__opt {{ $oldGroupId === '' ? 'is-active' : '' }}"
                  data-value=""
                  data-label="Pin pessoal"
                  role="option"
                  aria-selected="{{ $oldGroupId === '' ? 'true' : 'false' }}"
                >
                  Pin pessoal
                </button>

                @foreach($groups as $group)
                  <button
                    type="button"
                    class="pins-create-select__opt {{ (string) $oldGroupId === (string) $group->id ? 'is-active' : '' }}"
                    data-value="{{ $group->id }}"
                    data-label="{{ $group->name }}"
                    role="option"
                    aria-selected="{{ (string) $oldGroupId === (string) $group->id ? 'true' : 'false' }}"
                  >
                    {{ $group->name }}
                  </button>
                @endforeach
              </div>
            </div>

            <small class="pins-create-help">
              Mantém como pin pessoal ou escolhe um grupo para o partilhar.
            </small>

            @error('group_id')
              <small class="pins-create-error">
                {{ $message }}
              </small>
            @enderror
          </div>

          <div class="pins-create-field">
            <label for="pinContent">
              Descrição
            </label>

            <textarea
              id="pinContent"
              name="content"
              maxlength="5000"
              rows="5"
              class="@error('content') is-invalid @enderror"
              placeholder="Escreve uma nota, memória ou detalhe sobre este lugar..."
            >{{ $oldContent }}</textarea>

            @error('content')
              <small class="pins-create-error">
                {{ $message }}
              </small>
            @enderror
          </div>

          <div class="pins-create-field">
            <label for="locationText">
              Localização
            </label>

            <div class="pins-create-search">
              <i class="bi bi-search" aria-hidden="true"></i>

              <input
                type="text"
                name="location_text"
                id="locationText"
                value="{{ $oldLocation }}"
                maxlength="255"
                class="@error('location_text') is-invalid @enderror"
                placeholder="Ex: Praia da Nazaré"
                autocomplete="off"
              >

              <button type="button" id="mapSearchBtn" class="pins-create-search__btn">
                Pesquisar
              </button>
            </div>

            <small class="pins-create-help">
              Podes editar o local manualmente, pesquisar ou clicar diretamente no mapa.
            </small>

            @error('location_text')
              <small class="pins-create-error">
                {{ $message }}
              </small>
            @enderror
          </div>

          @if($currentImageUrl)
            <div class="pins-create-field">
              <label>
                Imagem atual
              </label>

              <div class="pins-create-current-image">
                <img
                  src="{{ $currentImageUrl }}"
                  alt="Imagem atual do pin {{ $pin->title ?? 'sem título' }}"
                  loading="lazy"
                  decoding="async"
                  onerror="this.closest('.pins-create-current-image').style.display='none';"
                >
              </div>

              <label class="pins-create-check-row" for="removeImageInput">
                <input
                  type="checkbox"
                  name="remove_image"
                  value="1"
                  id="removeImageInput"
                  @checked(old('remove_image'))
                >

                <span>Remover imagem atual</span>
              </label>

              <small class="pins-create-help">
                Se escolheres uma nova imagem, ela substitui automaticamente a imagem atual.
              </small>
            </div>
          @endif

          <div class="pins-create-field">
            <label for="imageInput">
              Nova imagem
            </label>

            <label class="pins-create-upload" for="imageInput">
              <input
                type="file"
                name="image"
                accept="image/jpeg,image/png,image/webp"
                id="imageInput"
              >

              <div class="pins-create-upload__inner">
                <i class="bi bi-image" aria-hidden="true"></i>

                <div class="pins-create-upload__text">
                  <strong id="uploadTitle">
                    Escolher nova imagem
                  </strong>

                  <span>
                    JPG, PNG ou WebP • máximo 4 MB
                  </span>
                </div>
              </div>
            </label>

            <img
              id="imagePreview"
              alt="Pré-visualização da nova imagem escolhida"
              class="pins-create-preview"
              style="display:none;"
            >

            @error('image')
              <small class="pins-create-error">
                {{ $message }}
              </small>
            @enderror
          </div>

          <div class="pins-create-field">
            <div class="pins-create-maphead">
              <div class="pins-create-maptitle">
                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                <span>Localização no mapa</span>
              </div>

              <div style="display:flex; gap:10px; flex-wrap:wrap;">
                <button type="button" id="useMyLocation" class="pins-create-mapbtn">
                  <i class="bi bi-crosshair" aria-hidden="true"></i>
                  <span>Usar localização atual</span>
                </button>

                <button type="button" id="clearLocation" class="pins-create-mapbtn">
                  <i class="bi bi-x-lg" aria-hidden="true"></i>
                  <span>Limpar</span>
                </button>
              </div>
            </div>

            <div
              id="pinMap"
              class="pins-create-map"
              data-lat="{{ $oldLat }}"
              data-lng="{{ $oldLng }}"
              aria-label="Mapa para editar a localização do pin"
            ></div>

            <div id="mapError" class="pins-create-maperror" hidden></div>

            <small class="pins-create-help">
              Clica no mapa para alterar a latitude e longitude do pin.
            </small>
          </div>

          <div class="pins-create-coordinates">
            <div class="pins-create-field">
              <label for="latInput">
                Latitude
              </label>

              <input
                id="latInput"
                name="lat"
                type="text"
                value="{{ $oldLat }}"
                readonly
                class="@error('lat') is-invalid @enderror"
              >

              @error('lat')
                <small class="pins-create-error">
                  {{ $message }}
                </small>
              @enderror
            </div>

            <div class="pins-create-field">
              <label for="lngInput">
                Longitude
              </label>

              <input
                id="lngInput"
                name="lng"
                type="text"
                value="{{ $oldLng }}"
                readonly
                class="@error('lng') is-invalid @enderror"
              >

              @error('lng')
                <small class="pins-create-error">
                  {{ $message }}
                </small>
              @enderror
            </div>
          </div>

          <div class="pins-create-actions">
            <button type="submit" class="pins-create-btn pins-create-btn--primary">
              <i class="bi bi-check-lg" aria-hidden="true"></i>
              <span>Guardar alterações</span>
            </button>

            <a href="{{ route('pins.show', $pin) }}" class="pins-create-btn pins-create-btn--secondary">
              <span>Cancelar</span>
            </a>
          </div>
        </form>
      </section>

      <aside class="pins-create-card pins-create-card--side" aria-labelledby="pin-edit-summary-title">
        <div class="pins-create-card__head">
          <div>
            <p class="pins-create-card__kicker">
              Resumo
            </p>

            <h2 id="pin-edit-summary-title" class="pins-create-card__title">
              Antes de guardar
            </h2>

            <p class="pins-create-card__subtitle">
              Confirma se a memória, localização e visibilidade estão corretas.
            </p>
          </div>
        </div>

        <div class="pins-create-summary">
          <div class="pins-create-summary__item">
            <span>
              <i class="bi bi-type" aria-hidden="true"></i>
              Título
            </span>

            <strong id="summaryTitle">
              {{ $oldTitle ?: 'Sem título' }}
            </strong>
          </div>

          <div class="pins-create-summary__item">
            <span>
              <i class="bi bi-collection" aria-hidden="true"></i>
              Tipo
            </span>

            <strong id="summaryGroup">
              {{ $selectedGroupText }}
            </strong>
          </div>

          <div class="pins-create-summary__item">
            <span>
              <i class="bi bi-geo-alt" aria-hidden="true"></i>
              Local
            </span>

            <strong id="summaryLocation">
              {{ $oldLocation ?: 'Sem localização' }}
            </strong>
          </div>

          <div class="pins-create-summary__item">
            <span>
              <i class="bi bi-crosshair" aria-hidden="true"></i>
              Coordenadas
            </span>

            <strong id="summaryCoords">
              @if($oldLat && $oldLng)
                {{ number_format((float) $oldLat, 5, '.', '') }}, {{ number_format((float) $oldLng, 5, '.', '') }}
              @else
                Por definir
              @endif
            </strong>
          </div>

          <div class="pins-create-summary__item">
            <span>
              <i class="bi bi-image" aria-hidden="true"></i>
              Imagem
            </span>

            <strong>
              {{ $currentImageUrl ? 'Com imagem' : 'Sem imagem' }}
            </strong>
          </div>
        </div>

        <div class="pins-create-note">
          <i class="bi bi-info-circle" aria-hidden="true"></i>

          <p>
            Se mudares este pin para um grupo, ele passa a aparecer no mapa partilhado desse grupo. Se o mantiveres como pessoal, fica apenas no teu mapa.
          </p>
        </div>
      </aside>

    </div>

  </div>
</main>

@endsection

@push('scripts')
  <script
    src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
    integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
    crossorigin=""
  ></script>

  @vite('resources/js/pins-create.js')
@endpush