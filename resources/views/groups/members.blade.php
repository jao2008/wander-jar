@extends('layouts.app')

@section('title', 'Membros do grupo — Wander Jar')
@section('page-id', 'groups.members')

@push('styles')
  @vite('resources/css/group-members.css')
@endpush

@section('content')

@php
  $isAdmin = ($role ?? 'member') === 'admin';
  $totalMembers = $members->count();
  $adminsCount = $adminsCount ?? $members->filter(fn($member) => (($member->pivot->role ?? 'member') === 'admin'))->count();
  $normalMembers = $members->filter(fn($member) => (($member->pivot->role ?? 'member') === 'member'))->count();
@endphp

@if (session('status'))
  <script>
    window.__FLASH_STATUS__ = @json(session('status'));
  </script>
@endif

<main class="gm-page">
  <div class="gm-container">

    <header class="gm-head">
      <div class="gm-left">
        <div class="gm-badge" aria-hidden="true">
          <i class="bi bi-people-fill"></i>
        </div>

        <div class="gm-titles">
          <p class="gm-kicker">
            Membros do grupo
          </p>

          <h1 class="gm-title">
            {{ $group->name }}
          </h1>

          <p class="gm-subtitle">
            Consulta os participantes do grupo e gere permissões de forma simples e organizada.
          </p>

          <div class="gm-pills" aria-label="Resumo dos membros do grupo">
            <span class="gm-pill">
              <i class="bi bi-people" aria-hidden="true"></i>
              <span>{{ $totalMembers }} participantes</span>
            </span>

            <span class="gm-pill gm-pill--soft">
              <i class="bi bi-person-badge" aria-hidden="true"></i>
              <span>{{ $adminsCount }} admins</span>
            </span>

            <span class="gm-pill gm-pill--soft">
              <i class="bi bi-person-check" aria-hidden="true"></i>
              <span>{{ $normalMembers }} membros</span>
            </span>
          </div>
        </div>
      </div>

      <div class="gm-actions">
        <a class="gm-btn gm-btn--ghost" href="{{ route('groups.index') }}">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
          <span>Voltar</span>
        </a>

        <a class="gm-btn gm-btn--ghost" href="{{ route('groups.map', $group) }}">
          <i class="bi bi-pin-map" aria-hidden="true"></i>
          <span>Abrir mapa</span>
        </a>

        @if(Route::has('groups.chat'))
          <a class="gm-btn gm-btn--primary" href="{{ route('groups.chat', $group) }}">
            <i class="bi bi-chat-dots" aria-hidden="true"></i>
            <span>Abrir chat</span>
          </a>
        @endif
      </div>
    </header>

    <section class="gm-panel" aria-labelledby="group-members-title">
      <div class="gm-panelHead">
        <div>
          <p class="gm-panelKicker">
            Participantes
          </p>

          <h2 id="group-members-title">
            Lista de membros
          </h2>

          <p>
            @if($isAdmin)
              Podes promover membros, remover participantes e manter o grupo organizado.
            @else
              Estás a consultar os membros deste grupo.
            @endif
          </p>
        </div>

        <span class="gm-roleBadge {{ $isAdmin ? 'is-admin' : 'is-member' }}">
          <i
            class="bi {{ $isAdmin ? 'bi-person-badge' : 'bi-person-check' }}"
            aria-hidden="true"
          ></i>

          <span>{{ $isAdmin ? 'Admin' : 'Membro' }}</span>
        </span>
      </div>

      <div class="gm-list">
        @forelse($members as $member)
          @php
            $memberRole = $member->pivot->role ?? 'member';
            $memberIsAdmin = $memberRole === 'admin';
            $isSelf = auth()->id() === $member->id;
            $photoUrl = $member->profile_photo
              ? route('profile.photo.show', $member)
              : null;
          @endphp

          <article class="gm-card">
            <div class="gm-memberMain">
              <div class="gm-avatar">
                @if($photoUrl)
                  <img
                    src="{{ $photoUrl }}"
                    alt="Foto de perfil de {{ $member->name }}"
                    onerror="this.style.display='none'; this.nextElementSibling.style.display='grid';"
                  >

                  <span style="display:none;" aria-hidden="true">
                    {{ mb_strtoupper(mb_substr($member->name ?? 'U', 0, 1)) }}
                  </span>
                @else
                  <span aria-hidden="true">
                    {{ mb_strtoupper(mb_substr($member->name ?? 'U', 0, 1)) }}
                  </span>
                @endif
              </div>

              <div class="gm-info">
                <div class="gm-nameRow">
                  <h3>
                    {{ $member->name }}
                  </h3>

                  @if($isSelf)
                    <span class="gm-selfBadge">
                      Tu
                    </span>
                  @endif
                </div>

                <p>
                  {{ $member->email }}
                </p>

                <span class="gm-roleBadge {{ $memberIsAdmin ? 'is-admin' : 'is-member' }}">
                  <i
                    class="bi {{ $memberIsAdmin ? 'bi-person-badge' : 'bi-person-check' }}"
                    aria-hidden="true"
                  ></i>

                  <span>{{ $memberIsAdmin ? 'Admin' : 'Membro' }}</span>
                </span>
              </div>
            </div>

            <div class="gm-memberActions">
              @if($isAdmin && !$isSelf)

                @if($memberIsAdmin)
                  <form
                    method="POST"
                    action="{{ route('groups.members.demote', [$group, $member]) }}"
                  >
                    @csrf
                    @method('PATCH')

                    <button class="gm-miniBtn gm-miniBtn--neutral" type="submit">
                      <i class="bi bi-arrow-down-circle" aria-hidden="true"></i>
                      <span>Tornar membro</span>
                    </button>
                  </form>
                @else
                  <form
                    method="POST"
                    action="{{ route('groups.members.promote', [$group, $member]) }}"
                  >
                    @csrf
                    @method('PATCH')

                    <button class="gm-miniBtn gm-miniBtn--promote" type="submit">
                      <i class="bi bi-arrow-up-circle" aria-hidden="true"></i>
                      <span>Promover</span>
                    </button>
                  </form>
                @endif

                <form
                  method="POST"
                  action="{{ route('groups.members.remove', [$group, $member]) }}"
                  data-confirm-remove
                >
                  @csrf
                  @method('DELETE')

                  <button class="gm-miniBtn gm-miniBtn--danger" type="submit">
                    <i class="bi bi-person-dash" aria-hidden="true"></i>
                    <span>Remover</span>
                  </button>
                </form>

              @else

                @if($isSelf)
                  <span class="gm-mutedAction">
                    <i class="bi bi-person-heart" aria-hidden="true"></i>
                    <span>A tua conta</span>
                  </span>
                @else
                  <span class="gm-mutedAction">
                    <i class="bi bi-eye" aria-hidden="true"></i>
                    <span>Apenas leitura</span>
                  </span>
                @endif

              @endif
            </div>
          </article>
        @empty
          <div class="gm-empty">
            <div class="gm-empty__icon" aria-hidden="true">
              <i class="bi bi-people"></i>
            </div>

            <h3>
              Ainda não existem membros.
            </h3>

            <p>
              Quando outras pessoas entrarem no grupo, irão aparecer aqui.
            </p>
          </div>
        @endforelse
      </div>
    </section>

    <div
      class="gm-toast"
      role="status"
      aria-live="polite"
      aria-atomic="true"
      hidden
    >
      <i class="bi bi-check2-circle" aria-hidden="true"></i>
      <span id="gmToastText"></span>
    </div>

  </div>
</main>

@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const toast = document.querySelector('.gm-toast');
      const toastText = document.getElementById('gmToastText');

      if (window.__FLASH_STATUS__ && toast && toastText) {
        toastText.textContent = window.__FLASH_STATUS__;
        toast.hidden = false;

        window.setTimeout(() => {
          toast.classList.add('is-visible');
        }, 40);

        window.setTimeout(() => {
          toast.classList.remove('is-visible');

          window.setTimeout(() => {
            toast.hidden = true;
          }, 250);
        }, 2800);
      }

      document.querySelectorAll('[data-confirm-remove]').forEach((form) => {
        form.addEventListener('submit', (event) => {
          const confirmed = window.confirm('Tens a certeza que queres remover este membro do grupo?');

          if (!confirmed) {
            event.preventDefault();
          }
        });
      });
    });
  </script>
@endpush