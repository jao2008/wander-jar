@extends('layouts.app')

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
    
    <div class="auth-card">
      
      <!-- Header -->
      <div class="auth-header">
        <div class="auth-icon">
          <i class="bi bi-shield-check"></i>
        </div>
        <h1 class="auth-title">Confirma a tua password</h1>
        <p class="auth-subtitle">Esta é uma área segura. Por favor, confirma a tua password antes de continuar.</p>
      </div>

      <!-- Form -->
      <form method="POST" action="{{ route('password.confirm') }}" class="auth-form">
        @csrf

        <!-- Password -->
        <div class="form-group">
          <label for="password" class="form-label">
            <i class="bi bi-lock"></i>
            Password
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
            <button type="button" class="password-toggle" aria-label="Mostrar password">
              <i class="bi bi-eye-slash"></i>
            </button>
          </div>
          @error('password')
            <span class="form-error">
              <i class="bi bi-exclamation-circle"></i>
              {{ $message }}
            </span>
          @enderror
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn-submit">
          <span>Confirmar</span>
          <i class="bi bi-check-circle"></i>
        </button>

        <!-- Back -->
        <div class="auth-footer">
          <a href="{{ url()->previous() }}" class="link-back">
            <i class="bi bi-arrow-left"></i>
            Voltar
          </a>
        </div>

      </form>

    </div>

  </div>
</main>

@endsection

@push('scripts')
  @vite('resources/js/confirm-password.js')
@endpush