@extends('layouts.app')

@push('styles')
  @vite('resources/css/errors.css')
@endpush

@section('content')

<main class="err-page">
  <div class="err-container">

    <section class="err-card">
      <div class="err-icon" aria-hidden="true">
        <i class="bi bi-exclamation-triangle"></i>
      </div>

      <p class="err-code">Erro 500</p>

      <h1 class="err-title">Algo correu mal</h1>

      <p class="err-text">
        Ocorreu um erro interno inesperado. Tenta novamente dentro de instantes.
        Se o problema continuar, volta ao dashboard e tenta outra ação.
      </p>

      <div class="err-actions">
        <a href="{{ route('dashboard') }}" class="err-btn err-btn--primary">
          <i class="bi bi-house"></i>
          <span>Ir para o dashboard</span>
        </a>

        <a href="{{ route('home') }}" class="err-btn err-btn--ghost">
          <i class="bi bi-arrow-clockwise"></i>
          <span>Voltar ao início</span>
        </a>
      </div>
    </section>

  </div>
</main>

@endsection