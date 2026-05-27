document.addEventListener('DOMContentLoaded', () => {
  const chat = document.getElementById('gcChat');
  const messagesEl = document.getElementById('gcMessages');
  const form = document.getElementById('gcForm');
  const input = document.getElementById('gcInput');

  const typingEl = document.getElementById('gcTyping');
  const typingTextEl = document.getElementById('gcTypingText');

  const emojiBtn = document.getElementById('gcEmojiBtn');
  const emojiPanel = document.getElementById('gcEmojiPanel');
  const emojiGrid = document.getElementById('gcEmojiGrid');
  const emojiSearch = document.getElementById('gcEmojiSearch');
  const emojiClose = document.getElementById('gcEmojiClose');
  const tabs = Array.from(document.querySelectorAll('.gc-tab'));

  if (!chat || !messagesEl || !form || !input) {
    return;
  }

  const groupId = chat.dataset.groupId;
  const authId = chat.dataset.authId;
  const authName = chat.dataset.authName || 'Utilizador';
  const postUrl = chat.dataset.postUrl;

  const EMOJIS = {
    recent: [],
    smileys: ['😀', '😁', '😂', '🤣', '😊', '😍', '🥰', '😘', '😎', '🥹', '😅', '😇', '🙂', '😉', '😴', '😤', '😡', '😭', '😳', '🤯', '😮', '🤔', '🙃', '🫠'],
    hands: ['👍', '👎', '👏', '🙏', '🤝', '👌', '✌️', '🤞', '🤟', '🫶', '💪', '👋', '🙌', '🫡'],
    hearts: ['❤️', '🩷', '🧡', '💛', '💚', '🩵', '💙', '💜', '🤍', '🖤', '🤎', '💖', '💘', '💝', '💞', '💕'],
    party: ['🎉', '🥳', '🎊', '✨', '🔥', '💫', '🎈', '🎁', '🍾', '🥂', '🎵', '🎶', '🏆'],
    nature: ['🌿', '🌸', '🌼', '🌙', '⭐', '☀️', '🌊', '🍃', '🏔️', '🌍', '🐾', '🦋'],
  };

  const RECENT_KEY = 'wj_chat_recent_emojis_v1';

  scrollToBottom(false);
  bootEmojiPicker();
  bootForm();
  bootRealtime();

  function scrollToBottom(smooth = true) {
    messagesEl.scrollTo({
      top: messagesEl.scrollHeight,
      behavior: smooth ? 'smooth' : 'auto',
    });
  }

  function bootEmojiPicker() {
    emojiBtn?.addEventListener('click', () => {
      const isOpen = emojiPanel && !emojiPanel.classList.contains('is-hidden');
      setPanelOpen(!isOpen);
    });

    emojiClose?.addEventListener('click', () => {
      setPanelOpen(false);
    });

    tabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const category = tab.dataset.cat || 'smileys';

        setActiveTab(category);
        renderEmojis(category, emojiSearch?.value || '');
      });
    });

    emojiGrid?.addEventListener('click', (event) => {
      const button = event.target.closest('.gc-emoji');

      if (!button) {
        return;
      }

      const emoji = button.dataset.emoji;

      if (!emoji) {
        return;
      }

      insertAtCursor(input, emoji);
      saveRecent(emoji);

      const activeCategory = document.querySelector('.gc-tab.is-active')?.dataset?.cat || 'smileys';

      if (activeCategory === 'recent') {
        renderEmojis('recent', emojiSearch?.value || '');
      }
    });

    emojiSearch?.addEventListener('input', () => {
      const activeCategory = document.querySelector('.gc-tab.is-active')?.dataset?.cat || 'smileys';

      renderEmojis(activeCategory, emojiSearch.value || '');
    });

    document.addEventListener('click', (event) => {
      if (!emojiPanel || emojiPanel.classList.contains('is-hidden')) {
        return;
      }

      const clickedInside = event.target.closest('#gcEmojiPanel') || event.target.closest('#gcEmojiBtn');

      if (!clickedInside) {
        setPanelOpen(false);
      }
    });

    document.addEventListener('keydown', (event) => {
      if (event.key !== 'Escape') {
        return;
      }

      if (!emojiPanel || emojiPanel.classList.contains('is-hidden')) {
        return;
      }

      setPanelOpen(false);
      input.focus();
    });
  }

  function setPanelOpen(open) {
    if (!emojiPanel || !emojiBtn) {
      return;
    }

    emojiPanel.classList.toggle('is-hidden', !open);
    emojiBtn.setAttribute('aria-expanded', open ? 'true' : 'false');

    if (open) {
      loadRecents();

      const defaultCategory = EMOJIS.recent.length > 0 ? 'recent' : 'smileys';

      setActiveTab(defaultCategory);
      renderEmojis(defaultCategory, '');

      setTimeout(() => {
        emojiSearch?.focus();
      }, 0);

      return;
    }

    if (emojiSearch) {
      emojiSearch.value = '';
    }
  }

  function setActiveTab(category) {
    tabs.forEach((tab) => {
      const isActive = tab.dataset.cat === category;

      tab.classList.toggle('is-active', isActive);
      tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });
  }

  function renderEmojis(category, query) {
    if (!emojiGrid) {
      return;
    }

    const cleanQuery = String(query || '').trim().toLowerCase();

    let list = [];

    if (cleanQuery) {
      list = Object.entries(EMOJIS)
        .filter(([key]) => key !== 'recent')
        .flatMap(([, value]) => value);
    } else {
      list = EMOJIS[category] || [];

      if (category === 'recent' && list.length === 0) {
        list = EMOJIS.smileys;
      }
    }

    emojiGrid.innerHTML = list
      .map((emoji) => {
        const safeEmoji = escapeAttr(emoji);

        return `
          <button
            type="button"
            class="gc-emoji"
            data-emoji="${safeEmoji}"
            aria-label="Emoji ${safeEmoji}"
          >
            ${emoji}
          </button>
        `;
      })
      .join('');
  }

  function loadRecents() {
    try {
      const raw = localStorage.getItem(RECENT_KEY);
      const parsed = raw ? JSON.parse(raw) : [];

      EMOJIS.recent = Array.isArray(parsed) ? parsed.slice(0, 24) : [];
    } catch {
      EMOJIS.recent = [];
    }
  }

  function saveRecent(emoji) {
    const current = EMOJIS.recent || [];
    const next = [emoji, ...current.filter((item) => item !== emoji)].slice(0, 24);

    EMOJIS.recent = next;

    try {
      localStorage.setItem(RECENT_KEY, JSON.stringify(next));
    } catch {
      // localStorage pode estar indisponível em alguns browsers/modos privados.
    }
  }

  function insertAtCursor(element, text) {
    if (!element) {
      return;
    }

    const start = element.selectionStart ?? element.value.length;
    const end = element.selectionEnd ?? element.value.length;

    element.value = element.value.slice(0, start) + text + element.value.slice(end);

    const nextPosition = start + text.length;

    element.setSelectionRange(nextPosition, nextPosition);
    element.focus();
  }

  function bootForm() {
    form.addEventListener('submit', async (event) => {
      event.preventDefault();

      const text = input.value.trim();

      if (!text || !postUrl) {
        return;
      }

      appendMessage({
        user_id: authId,
        user_name: authName,
        body: text,
        time: timeHHMM(),
      });

      input.value = '';
      input.focus();
      setPanelOpen(false);

      try {
        await sendMessage(text);
      } catch {
        showFormError('Não foi possível enviar a mensagem. Tenta novamente.');
      }
    });
  }

  async function sendMessage(text) {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const response = await fetch(postUrl, {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': token,
        'X-Requested-With': 'XMLHttpRequest',
        Accept: 'application/json',
      },
      body: new URLSearchParams({
        body: text,
      }),
    });

    if (!response.ok) {
      throw new Error('Falha ao enviar mensagem.');
    }

    try {
      return await response.json();
    } catch {
      return null;
    }
  }

  function appendMessage({ user_id, user_name, body, time }) {
    const empty = messagesEl.querySelector('.gc-empty');

    if (empty) {
      empty.remove();
    }

    const isMe = String(user_id) === String(authId);
    const message = document.createElement('article');

    message.className = `gc-msg ${isMe ? 'is-me' : ''}`;

    const avatar = isMe
      ? ''
      : `
        <div class="gc-avatar" aria-hidden="true">
          ${escapeHTML(String(user_name || 'U').charAt(0))}
        </div>
      `;

    message.innerHTML = `
      ${avatar}

      <div class="gc-bubble">
        <div class="gc-meta">
          <strong>${escapeHTML(user_name || 'Utilizador')}</strong>

          <time>${escapeHTML(time || '')}</time>
        </div>

        <p class="gc-text">${escapeHTML(body || '')}</p>
      </div>
    `;

    messagesEl.appendChild(message);
    scrollToBottom(true);
  }

  function bootRealtime() {
    if (!window.Echo || !groupId) {
      return;
    }

    const channel = window.Echo.join(`group.${groupId}`);

    channel.listen('.group.message', (event) => {
      const payload = event?.payload;

      if (!payload) {
        return;
      }

      const userId = payload.user_id;

      if (String(userId) === String(authId)) {
        return;
      }

      appendMessage({
        user_id: userId,
        user_name: payload.user_name || 'Utilizador',
        body: payload.body || '',
        time: payload.created_at ? timeHHMM(payload.created_at) : timeHHMM(),
      });
    });

    let typingTimeout = null;

    input.addEventListener('input', () => {
      channel.whisper('typing', {
        user_id: authId,
        name: authName,
      });
    });

    channel.listenForWhisper('typing', (data) => {
      if (!data || String(data.user_id) === String(authId)) {
        return;
      }

      if (!typingEl || !typingTextEl) {
        return;
      }

      typingTextEl.textContent = `${data.name || 'Alguém'} está a escrever…`;
      typingEl.classList.remove('is-hidden');

      clearTimeout(typingTimeout);

      typingTimeout = setTimeout(() => {
        typingEl.classList.add('is-hidden');
        typingTextEl.textContent = '';
      }, 1200);
    });
  }

  function showFormError(message) {
    const existing = document.querySelector('.gc-form-error');

    if (existing) {
      existing.remove();
    }

    const error = document.createElement('div');

    error.className = 'gc-form-error';
    error.textContent = message;

    form.prepend(error);

    setTimeout(() => {
      error.remove();
    }, 3200);
  }

  function timeHHMM(value) {
    const date = value ? new Date(value) : new Date();

    if (Number.isNaN(date.getTime())) {
      return '';
    }

    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');

    return `${hours}:${minutes}`;
  }

  function escapeHTML(value) {
    const div = document.createElement('div');

    div.textContent = String(value ?? '');

    return div.innerHTML;
  }

  function escapeAttr(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;');
  }
});