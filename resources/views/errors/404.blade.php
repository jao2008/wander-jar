@extends('layouts.app')

@push('styles')
  @vite('resources/css/errors.css')
@endpush

@section('content')

<main class="err-page">
  <div class="err-container">

    <section class="err-card">
      <div class="err-icon" aria-hidden="true">
        <i class="bi bi-compass"></i>
      </div>

      <p class="err-code">Erro 404</p>

      <h1 class="err-title">Página não encontrada</h1>

      <p class="err-text">
        A página que tentaste abrir não existe, foi movida ou o link já não é válido.
      </p>

      <div class="err-actions">
        <a href="{{ route('dashboard') }}" class="err-btn err-btn--primary">
          <i class="bi bi-house"></i>
          <span>Ir para o dashboard</span>
        </a>

        <a href="{{ route('home') }}" class="err-btn err-btn--ghost">
          <i class="bi bi-arrow-left"></i>
          <span>Voltar ao início</span>
        </a>
      </div>
    </section>

  </div>
</main>

@endsection