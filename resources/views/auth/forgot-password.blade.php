@extends('layouts.app')

@section('title', 'Recuperar palavra-passe — Wander Jar')
@section('page-id', 'auth.forgot-password')

@push('styles')
  @vite('resources/css/forgot-password.css')
@endpush

@section('content')

<div class="auth-bg" aria-hidden="true">
  <div class="auth-blob b1"></div>
  <div class="auth-blob b2"></div>
</div>

<main class="auth-page">
  <div class="container-auth-single">

    <section class="auth-card" aria-labelledby="forgot-password-title">

      <div class="auth-header">
        <div class="auth-icon" aria-hidden="true">
          <i class="bi bi-key"></i>
        </div>

        <h1 id="forgot-password-title" class="auth-title">
          Recuperar palavra-passe
        </h1>

        <p class="auth-subtitle">
          Introduz o teu email e enviaremos um link para criares uma nova palavra-passe.
        </p>
      </div>

      @if (session('status'))
        <div class="alert alert-success" role="status">
          <i class="bi bi-check-circle" aria-hidden="true"></i>
          <span>{{ session('status') }}</span>
        </div>
      @endif

      <form method="POST" action="{{ route('password.email') }}" class="auth-form" novalidate>
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

        <button type="submit" class="btn-submit">
          <span>Enviar link de recuperação</span>
          <i class="bi bi-send" aria-hidden="true"></i>
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
  @vite('resources/js/forgot-password.js')
@endpush