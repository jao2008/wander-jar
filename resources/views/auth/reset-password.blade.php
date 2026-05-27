@extends('layouts.app')

@section('title', 'Nova palavra-passe — Wander Jar')

@push('styles')
  @vite('resources/css/reset-password.css')
@endpush

@section('content')

<div class="auth-bg" aria-hidden="true">
  <div class="auth-blob b1"></div>
  <div class="auth-blob b2"></div>
</div>

<main class="auth-page">
  <div class="container-auth-single">

    <section class="auth-card" aria-labelledby="reset-password-title">

      <div class="auth-header">
        <div class="auth-icon" aria-hidden="true">
          <i class="bi bi-shield-lock"></i>
        </div>

        <h1 id="reset-password-title" class="auth-title">
          Criar nova palavra-passe
        </h1>

        <p class="auth-subtitle">
          Define uma nova palavra-passe segura para voltares a aceder à tua conta.
        </p>
      </div>

      <form method="POST" action="{{ route('password.store') }}" class="auth-form">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
          <label for="email" class="form-label">
            <i class="bi bi-envelope" aria-hidden="true"></i>
            <span>Email</span>
          </label>

          <input
            id="email"
            type="email"
            name="email"
            class="form-input @error('email') is-invalid @enderror"
            value="{{ old('email', $request->email) }}"
            required
            autofocus
            autocomplete="email"
            placeholder="teu@email.com"
          >

          @error('email')
            <span class="form-error">
              <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
              <span>{{ $message }}</span>
            </span>
          @enderror
        </div>

        <div class="form-group">
          <label for="password" class="form-label">
            <i class="bi bi-lock" aria-hidden="true"></i>
            <span>Nova palavra-passe</span>
          </label>

          <div class="password-wrapper">
            <input
              id="password"
              type="password"
              name="password"
              class="form-input @error('password') is-invalid @enderror"
              required
              autocomplete="new-password"
              placeholder="Mínimo 8 caracteres"
            >

            <button
              type="button"
              class="password-toggle"
              aria-label="Mostrar palavra-passe"
            >
              <i class="bi bi-eye-slash" aria-hidden="true"></i>
            </button>
          </div>

          @error('password')
            <span class="form-error">
              <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
              <span>{{ $message }}</span>
            </span>
          @enderror
        </div>

        <div class="form-group">
          <label for="password_confirmation" class="form-label">
            <i class="bi bi-lock-fill" aria-hidden="true"></i>
            <span>Confirmar palavra-passe</span>
          </label>

          <div class="password-wrapper">
            <input
              id="password_confirmation"
              type="password"
              name="password_confirmation"
              class="form-input"
              required
              autocomplete="new-password"
              placeholder="Repete a palavra-passe"
            >

            <button
              type="button"
              class="password-toggle"
              aria-label="Mostrar palavra-passe"
            >
              <i class="bi bi-eye-slash" aria-hidden="true"></i>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-submit">
          <span>Redefinir palavra-passe</span>
          <i class="bi bi-check-circle" aria-hidden="true"></i>
        </button>

        <div class="auth-footer">
          <a href="{{ route('login') }}" class="link-back">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            <span>Voltar para entrar</span>
          </a>
        </div>
      </form>

    </section>

  </div>
</main>

@endsection

@push('scripts')
  @vite('resources/js/reset-password.js')
@endpush