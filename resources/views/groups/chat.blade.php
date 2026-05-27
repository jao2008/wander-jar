@extends('layouts.app')

@section('title', 'Chat do grupo — Wander Jar')
@section('page-id', 'groups.chat')

@push('styles')
  @vite('resources/css/group-chat.css')
@endpush

@section('content')

@php
  $currentUser = auth()->user();
@endphp

<main class="gc-page">
  <div class="gc-container">

    <header class="gc-head">
      <div class="gc-head__left">
        <a class="gc-back" href="{{ route('groups.index') }}">
          <i class="bi bi-arrow-left" aria-hidden="true"></i>
          <span>Grupos</span>
        </a>

        <div class="gc-head__icon" aria-hidden="true">
          <i class="bi bi-chat-dots"></i>
        </div>

        <div class="gc-head__text">
          <p class="gc-kicker">
            Chat do grupo
          </p>

          <h1 class="gc-title">
            {{ $group->name }}
          </h1>

          <p class="gc-sub">
            Conversa em tempo real com os membros deste grupo.
          </p>
        </div>
      </div>

      <div class="gc-head__actions">
        <a class="gc-btn gc-btn--ghost" href="{{ route('groups.map', $group) }}">
          <i class="bi bi-map" aria-hidden="true"></i>
          <span>Mapa</span>
        </a>

        <a class="gc-btn gc-btn--ghost" href="{{ route('groups.members', $group) }}">
          <i class="bi bi-people" aria-hidden="true"></i>
          <span>Membros</span>
        </a>
      </div>
    </header>

    <section
      class="gc-chat"
      id="gcChat"
      data-group-id="{{ $group->id }}"
      data-auth-id="{{ $currentUser->id }}"
      data-auth-name="{{ $currentUser->name }}"
      data-post-url="{{ route('groups.chat.store', $group) }}"
      aria-label="Chat do grupo {{ $group->name }}"
    >

      <div class="gc-chat__top">
        <div>
          <h2 class="gc-chat__title">
            Mensagens
          </h2>

          <p class="gc-chat__subtitle">
            As mensagens novas aparecem automaticamente.
          </p>
        </div>

        <span class="gc-status">
          <span class="gc-status__dot" aria-hidden="true"></span>
          Em tempo real
        </span>
      </div>

      <div class="gc-messages" id="gcMessages" aria-live="polite">
        @forelse($messages as $message)
          @php
            $isMe = $message->user_id === auth()->id();
            $authorName = $message->user->name ?? 'Utilizador';
          @endphp

          <article class="gc-msg {{ $isMe ? 'is-me' : '' }}">
            @unless($isMe)
              <div class="gc-avatar" aria-hidden="true">
                {{ mb_substr($authorName, 0, 1) }}
              </div>
            @endunless

            <div class="gc-bubble">
              <div class="gc-meta">
                <strong>{{ $authorName }}</strong>

                <time datetime="{{ $message->created_at?->toIso8601String() }}">
                  {{ $message->created_at?->format('H:i') }}
                </time>
              </div>

              <p class="gc-text">
                {{ $message->body }}
              </p>
            </div>
          </article>
        @empty
          <div class="gc-empty" id="gcEmpty">
            <div class="gc-empty__icon" aria-hidden="true">
              <i class="bi bi-chat-dots"></i>
            </div>

            <h2 class="gc-empty__title">
              Ainda não há mensagens
            </h2>

            <p class="gc-empty__text">
              Escreve a primeira mensagem para começar a conversa deste grupo.
            </p>
          </div>
        @endforelse
      </div>

      <div class="gc-typing is-hidden" id="gcTyping" aria-live="polite">
        <span class="gc-typing__dots" aria-hidden="true">
          <i></i>
          <i></i>
          <i></i>
        </span>

        <span class="gc-typing__text" id="gcTypingText"></span>
      </div>

      <form class="gc-form" id="gcForm" autocomplete="off">
        @csrf

        <div class="gc-inputwrap">
          <button
            type="button"
            class="gc-emoji-btn"
            id="gcEmojiBtn"
            aria-label="Abrir emojis"
            aria-haspopup="dialog"
            aria-expanded="false"
          >
            <i class="bi bi-emoji-smile" aria-hidden="true"></i>
          </button>

          <input
            id="gcInput"
            class="gc-input"
            type="text"
            name="body"
            placeholder="Escreve uma mensagem..."
            maxlength="400"
            required
          >

          <button type="submit" class="gc-send" aria-label="Enviar mensagem">
            <i class="bi bi-send" aria-hidden="true"></i>
          </button>

          <div
            class="gc-emoji-panel is-hidden"
            id="gcEmojiPanel"
            role="dialog"
            aria-label="Selecionar emoji"
          >
            <div class="gc-emoji-top">
              <div class="gc-emoji-search">
                <i class="bi bi-search" aria-hidden="true"></i>

                <input
                  id="gcEmojiSearch"
                  type="text"
                  placeholder="Procurar emoji..."
                  autocomplete="off"
                >
              </div>

              <button
                type="button"
                class="gc-emoji-close"
                id="gcEmojiClose"
                aria-label="Fechar emojis"
              >
                <i class="bi bi-x-lg" aria-hidden="true"></i>
              </button>
            </div>

            <div class="gc-emoji-tabs" role="tablist" aria-label="Categorias de emojis">
              <button class="gc-tab is-active" type="button" data-cat="recent" role="tab" aria-selected="true">
                🕘
              </button>

              <button class="gc-tab" type="button" data-cat="smileys" role="tab">
                😀
              </button>

              <button class="gc-tab" type="button" data-cat="hands" role="tab">
                🤝
              </button>

              <button class="gc-tab" type="button" data-cat="hearts" role="tab">
                ❤️
              </button>

              <button class="gc-tab" type="button" data-cat="party" role="tab">
                🎉
              </button>

              <button class="gc-tab" type="button" data-cat="nature" role="tab">
                🌿
              </button>
            </div>

            <div class="gc-emoji-grid" id="gcEmojiGrid" aria-label="Lista de emojis"></div>

            <div class="gc-emoji-hint">
              Pressiona <kbd>Esc</kbd> para fechar.
            </div>
          </div>
        </div>
      </form>

    </section>

  </div>
</main>

@endsection

@push('scripts')
  @vite('resources/js/group-chat.js')
@endpush