@extends('layouts.app')

@section('title', 'Verificar email — Wander Jar')
@section('page-id', 'auth.verify-email')

@push('styles')
  @vite('resources/css/verify-email.css')
@endpush

@section('content')

<div class="auth-bg" aria-hidden="true">
  <div class="auth-blob b1"></div>
  <div class="auth-blob b2"></div>
</div>

<main class="auth-page">
  <div class="container-auth-single">

    <section class="auth-card" aria-labelledby="verify-email-title">

      <div class="auth-header">
        <div class="auth-icon" aria-hidden="true">
          <i class="bi bi-envelope-check"></i>
        </div>

        <h1 id="verify-email-title" class="auth-title">
          Verifica o teu email
        </h1>

        <p class="auth-subtitle">
          Antes de continuares no Wander Jar, confirma o teu endereço através do link que enviámos para o teu email.
        </p>
      </div>

      <div class="alert alert-info" role="status">
        <i class="bi bi-info-circle" aria-hidden="true"></i>

        <span>
          Para aceder ao dashboard, mapas, grupos, pins e eventos, precisas primeiro de verificar o teu email.
        </span>
      </div>

      @if (session('status') === 'verification-link-sent')
        <div class="alert alert-success" role="status">
          <i class="bi bi-check-circle" aria-hidden="true"></i>

          <span>
            Foi enviado um novo link de verificação para o teu email.
          </span>
        </div>
      @endif

      @if (session('status') && session('status') !== 'verification-link-sent')
        <div class="alert alert-success" role="status">
          <i class="bi bi-check-circle" aria-hidden="true"></i>

          <span>
            {{ session('status') }}
          </span>
        </div>
      @endif

      <div class="verify-actions">
        <form method="POST" action="{{ route('verification.send') }}" class="verify-form">
          @csrf

          <button type="submit" class="btn-submit">
            <span>Reenviar email de verificação</span>
            <i class="bi bi-send" aria-hidden="true"></i>
          </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="logout-form">
          @csrf

          <button type="submit" class="btn-logout">
            <i class="bi bi-box-arrow-right" aria-hidden="true"></i>
            <span>Sair da conta</span>
          </button>
        </form>
      </div>

    </section>

  </div>
</main>

@endsection

@push('scripts')
  @vite('resources/js/verify-email.js')
@endpush