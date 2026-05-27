@extends('layouts.app')

@section('title', 'Confirmar palavra-passe — Wander Jar')
@section('page-id', 'auth.confirm-password')

@push('styles')
  @vite('resources/css/confirm-password.css')
@endpush

@section('content')

<div class="auth-bg" aria-hidden="true">
  <div class="auth-blob b1"></div>
  <div class="auth-blob b2"></div>
</div>

<main class="auth-page">
  <div class="container-auth-single">

    <section class="auth-card" aria-labelledby="confirm-password-title">

      <div class="auth-header">
        <div class="auth-icon" aria-hidden="true">
          <i class="bi bi-shield-check"></i>
        </div>

        <h1 id="confirm-password-title" class="auth-title">
          Confirma a tua palavra-passe
        </h1>

        <p class="auth-subtitle">
          Esta é uma área segura. Confirma a tua palavra-passe antes de continuares.
        </p>
      </div>

      <form method="POST" action="{{ route('password.confirm') }}" class="auth-form" novalidate>
        @csrf

        <div class="form-group">
          <label for="password" class="form-label">
            <i class="bi bi-lock" aria-hidden="true"></i>
            <span>Palavra-passe</span>
          </label>

          <div class="password-wrapper">
            <input
              id="password"
              type="password"
              name="password"
              class="form-input @error('password') is-invalid @enderror"
              required
              autocomplete="current-password"
              placeholder="••••••••"
              autofocus
            >

            <button
              type="button"
              class="password-toggle"
              aria-label="Mostrar palavra-passe"
              aria-controls="password"
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

        <button type="submit" class="btn-submit">
          <span>Confirmar</span>
          <i class="bi bi-check-circle" aria-hidden="true"></i>
        </button>

        <div class="auth-footer">
          <a href="{{ url()->previous() }}" class="link-back">
            <i class="bi bi-arrow-left" aria-hidden="true"></i>
            <span>Voltar</span>
          </a>
        </div>
      </form>

    </section>

  </div>
</main>

@endsection

@push('scripts')
  @vite('resources/js/confirm-password.js')
@endpush