@extends('layouts.admin')

@section('page-title', 'Pins')

@section('content')

<div class="adm-page-header">
    <div class="adm-page-header-left">
        <div class="adm-page-eyebrow">
            <span class="adm-page-eyebrow-dot"></span>
            Gestão
        </div>

        <h1 class="adm-page-title">Pins</h1>

        <p class="adm-page-sub">
            {{ $pins->total() }} {{ $pins->total() === 1 ? 'pin registado' : 'pins registados' }} na plataforma.
        </p>
    </div>

    <div class="adm-page-header-right">
        <form method="GET" action="{{ route('admin.pins') }}" style="margin:0">
            <div class="adm-search">
                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? request('search') }}"
                    placeholder="Pesquisar por título ou localização"
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
                <th>Pin</th>
                <th>Autor</th>
                <th>Tipo</th>
                <th>Localização</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            @forelse($pins as $pin)
                @php
                    $pinTitle = $pin->title ?: 'Sem título';
                    $authorName = optional($pin->user)->name ?? 'Utilizador não identificado';
                    $groupName = optional($pin->group)->name ?? 'Grupo';
                    $isGroupPin = !empty($pin->group_id);
                @endphp

                <tr>
                    <td>
                        <div class="adm-td-name">
                            <div class="adm-td-av {{ $isGroupPin ? 'adm-td-av--purple' : '' }}">
                                <svg
                                    width="13"
                                    height="13"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    aria-hidden="true"
                                >
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                    <circle cx="12" cy="9" r="2.5"/>
                                </svg>
                            </div>

                            <span>{{ $pinTitle }}</span>
                        </div>
                    </td>

                    <td class="adm-td-muted">
                        {{ $authorName }}
                    </td>

                    <td>
                        @if($isGroupPin)
                            <span class="adm-badge adm-badge--purple">
                                <i class="bi bi-people-fill"></i>
                                {{ $groupName }}
                            </span>
                        @else
                            <span class="adm-badge adm-badge--gray">
                                <i class="bi bi-person-fill"></i>
                                Pessoal
                            </span>
                        @endif
                    </td>

                    <td class="adm-td-muted">
                        @if(!empty($pin->location_text))
                            <span title="{{ $pin->lat ?? '' }}{{ $pin->lat && $pin->lng ? ', ' : '' }}{{ $pin->lng ?? '' }}">
                                {{ $pin->location_text }}
                            </span>
                        @else
                            —
                        @endif
                    </td>

                    <td class="adm-td-muted">
                        {{ $pin->created_at?->format('d/m/Y') ?? '—' }}
                    </td>

                    <td>
                        <div class="adm-action-btns">
                            <form
                                method="POST"
                                action="{{ route('admin.pins.destroy', $pin) }}"
                                style="margin:0"
                                data-confirm="Apagar o pin &quot;{{ $pinTitle }}&quot;? Esta ação não pode ser revertida."
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="adm-action-btn adm-action-btn--danger"
                                    title="Apagar pin"
                                >
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="adm-empty">
                            @if(!empty($search ?? request('search')))
                                Não foram encontrados pins para “{{ $search ?? request('search') }}”.
                            @else
                                Ainda não existem pins criados.
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(method_exists($pins, 'links') && $pins->hasPages())
        <div class="adm-pagination">
            {{ $pins->links('pagination::simple-bootstrap-5') }}
        </div>
    @endif
</div>

@endsection