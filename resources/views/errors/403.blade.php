@extends('layouts.app')

@push('styles')
  @vite('resources/css/errors.css')
@endpush

@section('content')

<main class="err-page">
  <div class="err-container">

    <section class="err-card">
      <div class="err-icon" aria-hidden="true">
        <i class="bi bi-lock"></i>
      </div>

      <p class="err-code">Erro 403</p>

      <h1 class="err-title">Acesso restrito</h1>

      <p class="err-text">
        Não tens permissão para aceder a esta área.
        Isto normalmente acontece quando tentas abrir um grupo, chat ou conteúdo que não te pertence.
      </p>

      <div class="err-actions">
        <a href="{{ route('groups.index') }}" class="err-btn err-btn--primary">
          <i class="bi bi-people"></i>
          <span>Voltar aos grupos</span>
        </a>

        <a href="{{ route('dashboard') }}" class="err-btn err-btn--ghost">
          <i class="bi bi-house"></i>
          <span>Ir para o dashboard</span>
        </a>
      </div>
    </section>

  </div>
</main>

@endsection