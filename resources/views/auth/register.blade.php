@extends('layouts.app')

@section('title', 'Criar conta — Wander Jar')

@push('styles')
  @vite('resources/css/register.css')
@endpush

@section('content')

<div class="auth-bg" aria-hidden="true">
  <div class="auth-blob b1"></div>
  <div class="auth-blob b2"></div>
</div>

<main class="auth-page">
  <div class="container-auth">

    <section class="auth-card" aria-labelledby="register-title">

      <div class="auth-header">
        <div class="auth-icon" aria-hidden="true">
          <i class="bi bi-person-plus"></i>
        </div>

        <h1 id="register-title" class="auth-title">
          Cria a tua conta
        </h1>

        <p class="auth-subtitle">
          Começa a guardar experiências, criar pins e explorar novos planos.
        </p>
      </div>

      <form method="POST" action="{{ route('register') }}" class="auth-form">
        @csrf

        <div class="form-group">
          <label for="name" class="form-label">
            <i class="bi bi-person" aria-hidden="true"></i>
            <span>Nome</span>
          </label>

          <input
            id="name"
            type="text"
            name="name"
            class="form-input @error('name') is-invalid @enderror"
            value="{{ old('name') }}"
            required
            autofocus
            autocomplete="name"
            placeholder="O teu nome"
          >

          @error('name')
            <span class="form-error">
              <i class="bi bi-exclamation-circle" aria-hidden="true"></i>
              <span>{{ $message }}</span>
            </span>
          @enderror
        </div>

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
            autocomplete="email"
            placeholder="teu@email.com"
          >

          @error('email')
            <span class="form-error">
              <i class="bi bi-exclamation-circle" aria-hidden="true"></i>

              <span>
                @if (str_contains($message, 'already been taken') || str_contains($message, 'already exists') || str_contains($message, 'taken'))
                  Este email já está a ser utilizado.
                @else
                  {{ $message }}
                @endif
              </span>
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

        <div class="form-terms">
          <label class="checkbox-label" for="terms">
            <input
              type="checkbox"
              name="terms"
              id="terms"
              required
            >

            <span class="checkbox-custom" aria-hidden="true">
              <svg class="checkbox-icon" viewBox="0 0 12 10" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M1.5 5L4.5 8L10.5 1.5" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>

            <span>
              Aceito os
              <a href="#" class="link-inline">Termos de Serviço</a>
              e a
              <a href="#" class="link-inline">Política de Privacidade</a>
            </span>
          </label>
        </div>

        <button type="submit" class="btn-submit">
          <span>Criar conta</span>
          <i class="bi bi-arrow-right" aria-hidden="true"></i>
        </button>

        <div class="auth-footer">
          <p>Já tens conta?</p>

          <a href="{{ route('login') }}" class="link-register">
            Entrar agora
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
          Junta-te ao Wander Jar
        </h2>

        <p class="side-text">
          Cria o teu mapa pessoal, organiza locais especiais e partilha experiências com outras pessoas.
        </p>

        <div class="side-features">
          <div class="side-feature">
            <i class="bi bi-shield-check" aria-hidden="true"></i>
            <span>Conta pessoal segura</span>
          </div>

          <div class="side-feature">
            <i class="bi bi-pin-map" aria-hidden="true"></i>
            <span>Mapa privado para pins</span>
          </div>

          <div class="side-feature">
            <i class="bi bi-people" aria-hidden="true"></i>
            <span>Grupos, eventos e memórias</span>
          </div>
        </div>
      </div>
    </aside>

  </div>
</main>

@endsection

@push('scripts')
  @vite('resources/js/register.js')
@endpush