@extends('layouts.admin')

@section('page-title', 'Grupos')

@section('content')

<div class="adm-page-header">
    <div class="adm-page-header-left">
        <div class="adm-page-eyebrow">
            <span class="adm-page-eyebrow-dot"></span>
            Gestão
        </div>

        <h1 class="adm-page-title">Grupos</h1>

        <p class="adm-page-sub">
            {{ $groups->total() }} {{ $groups->total() === 1 ? 'grupo criado' : 'grupos criados' }} na plataforma.
        </p>
    </div>

    <div class="adm-page-header-right">
        <form method="GET" action="{{ route('admin.groups') }}" style="margin:0">
            <div class="adm-search">
                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? request('search') }}"
                    placeholder="Pesquisar por nome do grupo"
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
                <th>Grupo</th>
                <th>Acesso</th>
                <th>Membros</th>
                <th>Pins</th>
                <th>Criado em</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            @forelse($groups as $group)
                @php
                    $initial = mb_strtoupper(mb_substr($group->name ?? 'G', 0, 1));
                    $membersCount = $group->users_count ?? 0;
                    $pinsCount = $group->pins_count ?? 0;
                @endphp

                <tr>
                    <td>
                        <div class="adm-td-name">
                            <div class="adm-td-av adm-td-av--purple">
                                {{ $initial }}
                            </div>

                            <span>{{ $group->name }}</span>
                        </div>
                    </td>

                    <td>
                        <span class="adm-badge adm-badge--blue">
                            <i class="bi bi-link-45deg"></i>
                            Por convite
                        </span>
                    </td>

                    <td class="adm-td-muted">
                        {{ $membersCount }} {{ $membersCount === 1 ? 'membro' : 'membros' }}
                    </td>

                    <td class="adm-td-muted">
                        {{ $pinsCount }} {{ $pinsCount === 1 ? 'pin' : 'pins' }}
                    </td>

                    <td class="adm-td-muted">
                        {{ $group->created_at?->format('d/m/Y') ?? '—' }}
                    </td>

                    <td>
                        <div class="adm-action-btns">
                            <a
                                href="{{ route('groups.map', $group) }}"
                                class="adm-action-btn"
                                target="_blank"
                                rel="noopener noreferrer"
                                title="Ver mapa do grupo"
                            >
                                <i class="bi bi-map"></i>
                                Mapa
                            </a>

                            <form
                                method="POST"
                                action="{{ route('admin.groups.destroy', $group) }}"
                                style="margin:0"
                                data-confirm="Apagar o grupo &quot;{{ $group->name }}&quot;? Esta ação não pode ser revertida."
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    class="adm-action-btn adm-action-btn--danger"
                                    title="Apagar grupo"
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
                                Não foram encontrados grupos para “{{ $search ?? request('search') }}”.
                            @else
                                Ainda não existem grupos criados.
                            @endif
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(method_exists($groups, 'links') && $groups->hasPages())
        <div class="adm-pagination">
            {{ $groups->links('pagination::simple-bootstrap-5') }}
        </div>
    @endif
</div>

@endsection