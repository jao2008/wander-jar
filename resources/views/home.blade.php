@extends('layouts.app')

@section('title', 'Wander Jar — Guarda experiências e memórias')
@section('page-id', 'home')

@push('styles')
  @vite('resources/css/home.css')
@endpush

@section('content')

<main class="wj-page">

  <section class="wj-hero">
    <div class="container-xl">
      <div class="wj-heroGrid reveal">

        <div class="wj-heroCopy">
          <div class="wj-kicker">
            <span class="wj-kDot" aria-hidden="true"></span>
            <span class="wj-kTitle">Wander Jar</span>
            <span class="wj-kSep" aria-hidden="true">•</span>
            <span class="wj-kMuted">experiências e memórias</span>
          </div>

          <h1 class="wj-h1">
            <span class="wj-line">Guarda <span class="wj-grad">experiências</span></span>
            <span class="wj-line">e transforma planos</span>
            <span class="wj-line">em memórias.</span>
          </h1>

          <p class="wj-lead">
            Explora lugares, cria pins no mapa, organiza grupos e participa em eventos para guardar aquilo que vale a pena recordar.
          </p>

          <div class="wj-actions">
            @guest
              <a class="wj-btn wj-btnPrimary btn primary" href="{{ route('register') }}">
                <i class="bi bi-person-plus" aria-hidden="true"></i>
                <span>Criar conta</span>
              </a>

              <a class="wj-btn wj-btnSecondary btn" href="{{ route('login') }}">
                <i class="bi bi-box-arrow-in-right" aria-hidden="true"></i>
                <span>Entrar</span>
              </a>
            @else
              <a class="wj-btn wj-btnPrimary btn primary" href="{{ route('dashboard') }}">
                <i class="bi bi-grid" aria-hidden="true"></i>
                <span>Ir para o dashboard</span>
              </a>

              <a class="wj-btn wj-btnSecondary btn" href="{{ route('pins.create') }}">
                <i class="bi bi-plus-lg" aria-hidden="true"></i>
                <span>Criar pin</span>
              </a>
            @endguest

            <a class="wj-btn wj-btnGhost btn" href="#vibes">
              <i class="bi bi-compass" aria-hidden="true"></i>
              <span>Explorar ideias</span>
            </a>
          </div>
        </div>

        <div class="wj-preview-tilt" aria-hidden="true">
          <div class="wj-preview">

            <div class="wj-previewTop">
              <div class="wj-topLeft">
                <div class="wj-dotRow">
                  <span></span>
                  <span></span>
                  <span></span>
                </div>

                <div class="wj-previewTitle">
                  Descobrir
                </div>
              </div>

              <div class="wj-pill">
                Preview
              </div>
            </div>

            <div class="wj-search">
              <i class="bi bi-search"></i>
              <span>Procurar: café, trilhos, eventos...</span>
              <kbd>Enter</kbd>
            </div>

            <div class="wj-cards wj-cardsEqual">
              <article class="wj-card">
                <div class="wj-cardIcon">
                  <i class="bi bi-cup-hot"></i>
                </div>

                <div class="wj-cardTitle">
                  Comida & Cafés
                </div>

                <div class="wj-cardSub">
                  Lugares especiais para guardar e visitar.
                </div>
              </article>

              <article class="wj-card">
                <div class="wj-cardIcon">
                  <i class="bi bi-tree"></i>
                </div>

                <div class="wj-cardTitle">
                  Natureza
                </div>

                <div class="wj-cardSub">
                  Trilhos, miradouros e espaços ao ar livre.
                </div>
              </article>

              <article class="wj-card">
                <div class="wj-cardIcon">
                  <i class="bi bi-calendar-event"></i>
                </div>

                <div class="wj-cardTitle">
                  Eventos
                </div>

                <div class="wj-cardSub">
                  Experiências públicas para participar.
                </div>
              </article>

              <article class="wj-card">
                <div class="wj-cardIcon">
                  <i class="bi bi-people"></i>
                </div>

                <div class="wj-cardTitle">
                  Grupos
                </div>

                <div class="wj-cardSub">
                  Mapas partilhados com amigos.
                </div>
              </article>
            </div>

            <div class="wj-progress">
              <div class="wj-bar"></div>
            </div>

            <div class="wj-previewFoot">
              <span>Explorar</span>
              <span>Guardar</span>
              <span>Partilhar</span>
            </div>

          </div>
        </div>

      </div>
    </div>
  </section>

  <section id="vibes" class="wj-section">
    <div class="container-xl">
      <div class="wj-head reveal">
        <span class="wj-section-kicker">
          Inspiração
        </span>

        <h2 class="wj-h2">
          Escolhe a vibe
        </h2>

        <p class="wj-p">
          Começa por uma ideia, guarda o local no mapa e transforma o plano numa memória.
        </p>
      </div>

      <div class="wj-vibes reveal">

        <a
          class="wj-vibe"
          href="{{ auth()->check() ? route('pins.create') : route('login') }}"
          style="--img:url('https://images.unsplash.com/photo-1554118811-1e0d58224f24?w=800&h=600&fit=crop&q=80')"
        >
          <div class="wj-vFade"></div>

          <div class="wj-vTxt">
            <div class="wj-vTitle">Comida & Cafés</div>
            <div class="wj-vSub">Locais para experimentar</div>
          </div>
        </a>

        <a
          class="wj-vibe"
          href="{{ auth()->check() ? route('pins.create') : route('login') }}"
          style="--img:url('https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?w=800&h=600&fit=crop&q=80')"
        >
          <div class="wj-vFade"></div>

          <div class="wj-vTxt">
            <div class="wj-vTitle">Natureza</div>
            <div class="wj-vSub">Trilhos e miradouros</div>
          </div>
        </a>

        <a
          class="wj-vibe"
          href="{{ auth()->check() ? route('events.index') : route('login') }}"
          style="--img:url('https://images.unsplash.com/photo-1492684223066-81342ee5ff30?w=800&h=600&fit=crop&q=80')"
        >
          <div class="wj-vFade"></div>

          <div class="wj-vTxt">
            <div class="wj-vTitle">Eventos</div>
            <div class="wj-vSub">Cultura, encontros e experiências</div>
          </div>
        </a>

        <a
          class="wj-vibe wj-vibeTall"
          href="{{ auth()->check() ? route('pins.create') : route('login') }}"
          style="--img:url('https://images.unsplash.com/photo-1516339901601-2e1b62dc0c45?w=800&h=1200&fit=crop&q=80')"
        >
          <div class="wj-vFade"></div>

          <div class="wj-vTxt">
            <div class="wj-vTitle">Plano espontâneo</div>
            <div class="wj-vSub">Quando ainda não sabes o que fazer</div>
          </div>

          <span class="wj-tagOnImage">Inspiração</span>
        </a>

        <a
          class="wj-vibe"
          href="{{ auth()->check() ? route('pins.create') : route('login') }}"
          style="--img:url('https://images.unsplash.com/photo-1518998053901-5348d3961a04?w=800&h=600&fit=crop&q=80')"
        >
          <div class="wj-vFade"></div>

          <div class="wj-vTxt">
            <div class="wj-vTitle">Cultura</div>
            <div class="wj-vSub">Museus, passeios e descobertas</div>
          </div>
        </a>

        <a
          class="wj-vibe"
          href="{{ auth()->check() ? route('pins.create') : route('login') }}"
          style="--img:url('https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=800&h=600&fit=crop&q=80')"
        >
          <div class="wj-vFade"></div>

          <div class="wj-vTxt">
            <div class="wj-vTitle">Noite</div>
            <div class="wj-vSub">Rooftops, música e saídas</div>
          </div>
        </a>

      </div>
    </div>
  </section>

  <section class="wj-section">
    <div class="container-xl">
      <div class="wj-head reveal">
        <span class="wj-section-kicker">
          Funcionalidades
        </span>

        <h2 class="wj-h2">
          Tudo o que precisas
        </h2>

        <p class="wj-p">
          Ferramentas simples para organizar lugares, grupos, conversas e eventos.
        </p>
      </div>

      <div class="wj-features reveal">

        <article class="wj-feature">
          <div class="wj-featureIcon" aria-hidden="true">
            <i class="bi bi-pin-map"></i>
          </div>

          <h3 class="wj-featureTitle">
            Mapa pessoal
          </h3>

          <p class="wj-featureDesc">
            Guarda pins privados com localização, descrição e imagem para criares o teu próprio mapa de memórias.
          </p>
        </article>

        <article class="wj-feature">
          <div class="wj-featureIcon" aria-hidden="true">
            <i class="bi bi-people"></i>
          </div>

          <h3 class="wj-featureTitle">
            Grupos partilhados
          </h3>

          <p class="wj-featureDesc">
            Cria grupos, partilha pins e conversa em tempo real com outras pessoas através do chat.
          </p>
        </article>

        <article class="wj-feature">
          <div class="wj-featureIcon" aria-hidden="true">
            <i class="bi bi-calendar-event"></i>
          </div>

          <h3 class="wj-featureTitle">
            Eventos
          </h3>

          <p class="wj-featureDesc">
            Descobre, cria e participa em eventos para transformar planos em experiências reais.
          </p>
        </article>

        <article class="wj-feature">
          <div class="wj-featureIcon" aria-hidden="true">
            <i class="bi bi-stars"></i>
          </div>

          <h3 class="wj-featureTitle">
            Memórias organizadas
          </h3>

          <p class="wj-featureDesc">
            Mantém os teus lugares especiais guardados num espaço visual, simples e fácil de consultar.
          </p>
        </article>

      </div>
    </div>
  </section>

  <section class="wj-section wj-sectionAlt">
    <div class="container-xl">
      <div class="wj-steps reveal">

        <div class="wj-stepsCopy">
          <span class="wj-section-kicker">
            Como funciona
          </span>

          <h2 class="wj-h2">
            De uma ideia a uma memória em poucos passos.
          </h2>

          <p class="wj-p">
            O Wander Jar foi pensado para ser direto: guardas um local, organizas a experiência e podes partilhar com outras pessoas.
          </p>

          <div class="wj-actions">
            @guest
              <a class="wj-btn wj-btnPrimary btn primary" href="{{ route('register') }}">
                Começar agora
              </a>
            @else
              <a class="wj-btn wj-btnPrimary btn primary" href="{{ route('dashboard') }}">
                Abrir dashboard
              </a>
            @endguest
          </div>
        </div>

        <div class="wj-stepList">
          <article class="wj-step">
            <span class="wj-stepNumber">01</span>

            <div>
              <h3 class="wj-stepTitle">
                Cria um pin
              </h3>

              <p class="wj-stepText">
                Escolhe uma localização no mapa, adiciona descrição e guarda o momento.
              </p>
            </div>
          </article>

          <article class="wj-step">
            <span class="wj-stepNumber">02</span>

            <div>
              <h3 class="wj-stepTitle">
                Organiza em grupos
              </h3>

              <p class="wj-stepText">
                Partilha mapas com amigos e mantém tudo centralizado no mesmo espaço.
              </p>
            </div>
          </article>

          <article class="wj-step">
            <span class="wj-stepNumber">03</span>

            <div>
              <h3 class="wj-stepTitle">
                Participa em eventos
              </h3>

              <p class="wj-stepText">
                Cria planos, junta participantes e transforma ideias em experiências reais.
              </p>
            </div>
          </article>
        </div>

      </div>
    </div>
  </section>

  <section class="wj-section">
    <div class="container-xl">
      <div class="wj-cta reveal">
        <div>
          <span class="wj-section-kicker">
            Começar
          </span>

          <h2 class="wj-ctaTitle">
            Pronta para construir o teu mapa de memórias?
          </h2>

          <p class="wj-ctaText">
            Guarda lugares, combina experiências e mantém os teus melhores momentos organizados.
          </p>
        </div>

        <div class="wj-ctaActions">
          @guest
            <a class="wj-btn wj-btnPrimary btn primary" href="{{ route('register') }}">
              Criar conta
            </a>

            <a class="wj-btn wj-btnSecondary btn" href="{{ route('login') }}">
              Entrar
            </a>
          @else
            <a class="wj-btn wj-btnPrimary btn primary" href="{{ route('pins.create') }}">
              Criar pin
            </a>

            <a class="wj-btn wj-btnSecondary btn" href="{{ route('map') }}">
              Ver mapa
            </a>
          @endguest
        </div>
      </div>
    </div>
  </section>

</main>

@endsection

@push('scripts')
  @vite('resources/js/home.js')
@endpush