@extends('layouts.admin')

@section('page-title', 'Eventos')

@section('content')

<div class="adm-page-header">
    <div class="adm-page-header-left">
        <div class="adm-page-eyebrow">
            <span class="adm-page-eyebrow-dot"></span>
            Gestão
        </div>

        <h1 class="adm-page-title">Eventos</h1>

        <p class="adm-page-sub">
            {{ $events->total() }} {{ $events->total() === 1 ? 'evento registado' : 'eventos registados' }} na plataforma.
        </p>
    </div>

    <div class="adm-page-header-right">
        <form method="GET" action="{{ route('admin.events') }}" style="margin:0">
            <div class="adm-search">
                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? request('search') }}"
                    placeholder="Pesquisar por evento ou localização"
                    autocomplete="off"
                >
            </div>
        </form>
    </div>
</div>

<div class="adm-table-wrap">
    <table class="adm-table">
        <thead>
            <tr>
                <th>Evento</th>
                <th>Criador</th>
                <th>Data</th>
                <th>Local</th>
                <th>Participantes</th>
                <th>Estado</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            @forelse($events as $event)
                @php
                    $eventTitle = $event->title ?: 'Sem título';
                    $creatorName = optional($event->creator)->name ?? 'Utilizador não identificado';

                    $status = method_exists($event, 'statusBadge')
                        ? $event->statusBadge()
                        : [
                            'key' => $event->status ?? 'active',
                            'label' => ucfirst($event->status ?? 'Ativo'),
                        ];

                    $statusKey = $status['key'] ?? 'active';
                    $statusLabel = $status['label'] ?? 'Ativo';

                    $badgeClass = match($statusKey) {
                        'active' => 'adm-badge--green',
                        'full' => 'adm-badge--blue',
                        'cancelled' => 'adm-badge--gray',
                        'past' => 'adm-badge--gray',
                        default => 'adm-badge--gray',
                    };

                    $canCancel = $statusKey === 'active';
                    $participantsCount = $event->participants_count ?? 0;
                    $maxParticipants = $event->max_participants ?? null;
                @endphp

                <tr>
                    <td>
                        <div class="adm-td-name">
                            <div class="adm-td-av adm-td-av--amber">
                                <i class="bi bi-calendar-event" aria-hidden="true"></i>
                            </div>

                            <span>{{ $eventTitle }}</span>
                        </div>
                    </td>

                    <td class="adm-td-muted">
                        {{ $creatorName }}
                    </td>

                    <td class="adm-td-muted">
                        @if($event->event_date)
                            {{ $event->event_date->format('d/m/Y') }}

                            @if($event->event_time)
                                <span style="opacity:.55">
                                    · {{ \Carbon\Carbon::parse($event->event_time)->format('H:i') }}
                                </span>
                            @endif
                        @else
                            —
                        @endif
                    </td>

                    <td class="adm-td-muted">
                        {{ $event->location_text ?: '—' }}
                    </td>

                    <td class="adm-td-muted">
                        {{ $participantsCount }}

                        @if($maxParticipants)
                            <span style="opacity:.55">
                                / {{ $maxParticipants }}
                            </span>
                        @endif
                    </td>

                    <td>
                        <span class="adm-badge {{ $badgeClass }}">
                            {{ $statusLabel }}
                        </span>
                    </td>

                    <td>
                        <div class="adm-action-btns">
                            @if($canCancel)
                                <form
                                    method="POST"
                                    action="{{ route('admin.events.cancel', $event) }}"
                                    style="margin:0"
                                    data-confirm="Cancelar o evento &quot;{{ $eventTitle }}&quot;?"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="adm-action-btn"
                                        title="Cancelar evento"
                                    >
                                        <i class="bi bi-x-circle"></i>
                                        Cancelar
                                    </button>
                                </form>
                            @endif

                            <form
                                method="POST"
                                action="{{ route('admin.events.destroy', $event) }}"
                                style="margin:0"
                                data-confirm="Apagar o evento &quot;{{ $eventTitle }}&quot;? Esta ação não pode ser revertida."
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="adm-action-btn adm-action-btn--danger"
                                    title="Apagar evento"
                                >
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">
                        <div class="adm-empty">
                            @if(!empty($search ?? request('search')))
                                Não foram encontrados eventos para “{{ $search ?? request('search') }}”.
                            @else
                                Ainda não existem eventos criados.
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(method_exists($events, 'links') && $events->hasPages())
        <div class="adm-pagination">
            {{ $events->links('pagination::simple-bootstrap-5') }}
        </div>
    @endif
</div>

@endsection