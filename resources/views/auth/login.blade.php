@extends('layouts.app')

@section('title', 'Entrar — Wander Jar')
@section('page-id', 'auth.login')

@push('styles')
  @vite('resources/css/login.css')
@endpush

@section('content')

<div class="auth-bg" aria-hidden="true">
  <div class="auth-blob b1"></div>
  <div class="auth-blob b2"></div>
</div>

<main class="auth-page">
  <div class="container-auth">

    <section class="auth-card" aria-labelledby="login-title">

      <div class="auth-header">
        <div class="auth-icon" aria-hidden="true">
          <i class="bi bi-box-arrow-in-right"></i>
        </div>

        <h1 id="login-title" class="auth-title">
          Bem-vindo de volta
        </h1>

        <p class="auth-subtitle">
          Entra na tua conta para continuares a guardar e organizar experiências.
        </p>
      </div>

      @if (session('status'))
        <div class="alert alert-success" role="status">
          <i class="bi bi-check-circle" aria-hidden="true"></i>
          <span>{{ session('status') }}</span>
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}" class="auth-form" novalidate>
        @csrf

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
            value="{{ old('email') }}"
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

        <div class="form-options">
          <label class="checkbox-label" for="remember">
            <input
              type="checkbox"
              name="remember"
              id="remember"
              {{ old('remember') ? 'checked' : '' }}
            >

            <span class="checkbox-custom" aria-hidden="true">
              <svg class="checkbox-icon" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.5 5L4.5 8L10.5 1.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>

            <span>Lembrar-me</span>
          </label>

          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="link-forgot">
              Esqueci-me da palavra-passe
            </a>
          @endif
        </div>

        <button type="submit" class="btn-submit">
          <span>Entrar</span>
          <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </button>

        <div class="auth-footer">
          <p>Ainda não tens conta?</p>

          <a href="{{ route('register') }}" class="link-register">
            Criar conta gratuita
            <i class="bi bi-arrow-right" aria-hidden="true"></i>
          </a>
        </div>
      </form>

    </section>

    <aside class="auth-side" aria-label="Resumo do Wander Jar">
      <div class="side-content">
        <div class="side-icon" aria-hidden="true">
          <i class="bi bi-bookmark-heart-fill"></i>
        </div>

        <h2 class="side-title">
          Transforma planos em memórias
        </h2>

        <p class="side-text">
          Guarda experiências, organiza ideias e descobre sempre algo novo para fazer.
        </p>

        <div class="side-features">
          <div class="side-feature">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <span>Organiza os teus planos</span>
          </div>

          <div class="side-feature">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <span>Partilha com amigos</span>
          </div>

          <div class="side-feature">
            <i class="bi bi-check-circle-fill" aria-hidden="true"></i>
            <span>Descobre novas experiências</span>
          </div>
        </div>
      </div>
    </aside>

  </div>
</main>

@endsection

@push('scripts')
  @vite('resources/js/login.js')
@endpush