@extends('layouts.app')

@section('title', 'Perfil — Wander Jar')
@section('page-id', 'profile.edit')

@push('styles')
  @vite('resources/css/profile.css')
@endpush

@section('content')

@php
  $user = auth()->user();
  $initial = mb_strtoupper(mb_substr($user->name ?? 'U', 0, 1));
@endphp

<main class="profile-page">
  <div class="profile-container">

    <header class="profile-header">
      <div class="profile-welcome">
        <div class="profile-icon" aria-hidden="true">
          <i class="bi bi-person-circle"></i>
        </div>

        <div>
          <p class="profile-kicker">
            Conta
          </p>

          <h1 class="profile-title">
            O meu perfil
          </h1>

          <p class="profile-subtitle">
            Gere a tua informação pessoal, foto de perfil e segurança da conta.
          </p>
        </div>
      </div>

      <a class="btn-back" href="{{ route('dashboard') }}">
        <i class="bi bi-arrow-left" aria-hidden="true"></i>
        <span>Voltar</span>
      </a>
    </header>

    @if (session('status'))
      <div class="profile-alert profile-alert--success" role="status">
        <i class="bi bi-check-circle" aria-hidden="true"></i>
        <span>{{ session('status') }}</span>
      </div>
    @endif

    @if ($errors->any())
      <div class="profile-alert profile-alert--danger" role="alert">
        <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>

        <div>
          <strong>Existem erros no formulário.</strong>

          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      </div>
    @endif

    <div class="profile-grid">

      <section class="profile-card profile-card--photo" aria-labelledby="profile-photo-title">
        <div class="card-header">
          <div>
            <p class="card-kicker">
              Imagem
            </p>

            <h2 id="profile-photo-title" class="card-title">
              Foto de perfil
            </h2>

            <p class="card-subtitle">
              Adiciona ou atualiza a imagem associada à tua conta.
            </p>
          </div>
        </div>

        <div class="photo-section">
          <div class="photo-preview" id="photoPreview">
            @if($user->profile_photo)
              <img
                src="{{ route('profile.photo.show', $user->id) }}?v={{ $user->updated_at?->timestamp }}"
                alt="Foto de perfil de {{ $user->name }}"
                id="profilePhotoPreview"
                loading="lazy"
                decoding="async"
                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
              >

              <div class="photo-placeholder" style="display:none;" aria-hidden="true">
                <span>{{ $initial }}</span>
              </div>
            @else
              <img
                src=""
                alt=""
                id="profilePhotoPreview"
                style="display:none;"
              >

              <div class="photo-placeholder" aria-hidden="true">
                <span>{{ $initial }}</span>
              </div>
            @endif
          </div>

          <div class="photo-actions">
            <form
              method="POST"
              action="{{ route('profile.update.photo') }}"
              enctype="multipart/form-data"
              class="photo-form"
            >
              @csrf

              <input
                type="file"
                name="profile_photo"
                id="profilePhoto"
                accept="image/jpeg,image/png,image/webp,image/gif"
                hidden
              >

              <div class="photo-buttons">
                <label for="profilePhoto" class="profile-btn profile-btn--secondary">
                  <i class="bi bi-upload" aria-hidden="true"></i>
                  <span>Escolher foto</span>
                </label>

                <button type="submit" class="profile-btn profile-btn--primary" id="savePhotoBtn" disabled>
                  <i class="bi bi-check2" aria-hidden="true"></i>
                  <span>Guardar foto</span>
                </button>
              </div>

              @error('profile_photo')
                <p class="error-text">
                  {{ $message }}
                </p>
              @enderror
            </form>

            @if($user->profile_photo)
              <form method="POST" action="{{ route('profile.delete.photo') }}" class="photo-delete-form">
                @csrf
                @method('DELETE')

                <button type="submit" class="profile-btn profile-btn--danger">
                  <i class="bi bi-trash3" aria-hidden="true"></i>
                  <span>Remover foto</span>
                </button>
              </form>
            @endif
          </div>
        </div>

        <p class="card-hint">
          <i class="bi bi-info-circle" aria-hidden="true"></i>
          Formatos aceites: JPG, PNG, WebP ou GIF. Tamanho máximo: 2 MB.
        </p>
      </section>

      <section class="profile-card" aria-labelledby="profile-info-title">
        <div class="card-header">
          <div>
            <p class="card-kicker">
              Dados
            </p>

            <h2 id="profile-info-title" class="card-title">
              Informação pessoal
            </h2>

            <p class="card-subtitle">
              Atualiza o teu nome e endereço de email.
            </p>
          </div>
        </div>

        <div class="profile-forms-stack">
          <form method="POST" action="{{ route('profile.update.name') }}" class="profile-form">
            @csrf
            @method('PATCH')

            <div class="form-group">
              <label for="name">
                Nome
              </label>

              <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $user->name) }}"
                required
                maxlength="255"
                autocomplete="name"
              >

              @error('name')
                <p class="error-text">
                  {{ $message }}
                </p>
              @enderror
            </div>

            <button type="submit" class="profile-btn profile-btn--primary">
              <i class="bi bi-check2" aria-hidden="true"></i>
              <span>Guardar nome</span>
            </button>
          </form>

          <form method="POST" action="{{ route('profile.update.email') }}" class="profile-form">
            @csrf
            @method('PATCH')

            <div class="form-group">
              <label for="email">
                Email
              </label>

              <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email', $user->email) }}"
                required
                maxlength="255"
                autocomplete="email"
              >

              @error('email')
                <p class="error-text">
                  {{ $message }}
                </p>
              @enderror

              @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="hint-text">
                  <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
                  Este email ainda não foi verificado.
                </p>
              @endif
            </div>

            <button type="submit" class="profile-btn profile-btn--primary">
              <i class="bi bi-envelope-check" aria-hidden="true"></i>
              <span>Guardar email</span>
            </button>
          </form>
        </div>
      </section>

      <section class="profile-card" aria-labelledby="profile-password-title">
        <div class="card-header">
          <div>
            <p class="card-kicker">
              Segurança
            </p>

            <h2 id="profile-password-title" class="card-title">
              Alterar palavra-passe
            </h2>

            <p class="card-subtitle">
              Usa uma palavra-passe forte para manteres a tua conta protegida.
            </p>
          </div>
        </div>

        <form method="POST" action="{{ route('profile.update.password') }}" class="profile-form">
          @csrf
          @method('PATCH')

          <div class="form-group">
            <label for="current_password">
              Palavra-passe atual
            </label>

            <input
              type="password"
              name="current_password"
              id="current_password"
              required
              autocomplete="current-password"
            >

            @error('current_password')
              <p class="error-text">
                {{ $message }}
              </p>
            @enderror
          </div>

          <div class="form-group">
            <label for="password">
              Nova palavra-passe
            </label>

            <input
              type="password"
              name="password"
              id="password"
              required
              autocomplete="new-password"
            >

            @error('password')
              <p class="error-text">
                {{ $message }}
              </p>
            @enderror
          </div>

          <div class="form-group">
            <label for="password_confirmation">
              Confirmar nova palavra-passe
            </label>

            <input
              type="password"
              name="password_confirmation"
              id="password_confirmation"
              required
              autocomplete="new-password"
            >
          </div>

          <button type="submit" class="profile-btn profile-btn--primary">
            <i class="bi bi-shield-check" aria-hidden="true"></i>
            <span>Atualizar palavra-passe</span>
          </button>
        </form>
      </section>

      <aside class="profile-card profile-card--summary" aria-labelledby="profile-summary-title">
        <div class="card-header">
          <div>
            <p class="card-kicker">
              Resumo
            </p>

            <h2 id="profile-summary-title" class="card-title">
              A tua conta
            </h2>

            <p class="card-subtitle">
              Informação principal da tua conta no Wander Jar.
            </p>
          </div>
        </div>

        <div class="profile-summary">
          <div class="profile-summary-item">
            <span>
              <i class="bi bi-person" aria-hidden="true"></i>
              Nome
            </span>

            <strong>
              {{ $user->name }}
            </strong>
          </div>

          <div class="profile-summary-item">
            <span>
              <i class="bi bi-envelope" aria-hidden="true"></i>
              Email
            </span>

            <strong>
              {{ $user->email }}
            </strong>
          </div>

          <div class="profile-summary-item">
            <span>
              <i class="bi bi-check-circle" aria-hidden="true"></i>
              Verificação
            </span>

            <strong>
              @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail)
                {{ $user->hasVerifiedEmail() ? 'Verificado' : 'Por verificar' }}
              @else
                Não necessária
              @endif
            </strong>
          </div>

          <div class="profile-summary-item">
            <span>
              <i class="bi bi-image" aria-hidden="true"></i>
              Foto
            </span>

            <strong>
              {{ $user->profile_photo ? 'Com foto' : 'Sem foto' }}
            </strong>
          </div>

          <div class="profile-summary-item">
            <span>
              <i class="bi bi-calendar3" aria-hidden="true"></i>
              Conta criada
            </span>

            <strong>
              {{ optional($user->created_at)->format('d/m/Y') ?? '—' }}
            </strong>
          </div>
        </div>

        <p class="card-hint" style="margin-top:16px;">
          <i class="bi bi-info-circle" aria-hidden="true"></i>
          Mantém os teus dados atualizados para uma melhor experiência no Wander Jar.
        </p>
      </aside>

    </div>
  </div>
</main>

@endsection

@push('scripts')
  @vite('resources/js/profile.js')
@endpush