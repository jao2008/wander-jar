@extends('layouts.admin')

@section('page-title', 'Utilizadores')

@section('content')

<div class="adm-page-header">
    <div class="adm-page-header-left">
        <div class="adm-page-eyebrow">
            <span class="adm-page-eyebrow-dot"></span>
            Gestão
        </div>

        <h1 class="adm-page-title">Utilizadores</h1>

        <p class="adm-page-sub">
            {{ $users->total() }} {{ $users->total() === 1 ? 'utilizador registado' : 'utilizadores registados' }} na plataforma.
        </p>
    </div>

    <div class="adm-page-header-right">
        <form method="GET" action="{{ route('admin.users') }}" style="margin:0">
            <div class="adm-search">
                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="search"
                    value="{{ $search ?? request('search') }}"
                    placeholder="Pesquisar por nome ou email"
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
                <th>Utilizador</th>
                <th>Email</th>
                <th>Verificação</th>
                <th>Registo</th>
                <th>Papel</th>
                <th>Ações</th>
            </tr>
        </thead>

        <tbody>
            @forelse($users as $user)
                @php
                    $isCurrentUser = $user->id === auth()->id();
                    $initial = mb_strtoupper(mb_substr($user->name ?? 'U', 0, 1));
                @endphp

                <tr>
                    <td>
                        <div class="adm-td-name">
                            <div class="adm-td-av {{ $user->is_admin ? 'adm-td-av--amber' : '' }}">
                                {{ $initial }}
                            </div>

                            <span>{{ $user->name }}</span>
                        </div>
                    </td>

                    <td class="adm-td-muted">
                        {{ $user->email }}
                    </td>

                    <td>
                        @if($user->email_verified_at)
                            <span class="adm-badge adm-badge--green">
                                <i class="bi bi-check-circle-fill"></i>
                                Verificado
                            </span>
                        @else
                            <span class="adm-badge adm-badge--gray">
                                <i class="bi bi-clock"></i>
                                Pendente
                            </span>
                        @endif
                    </td>

                    <td class="adm-td-muted">
                        {{ $user->created_at?->format('d/m/Y') ?? '—' }}
                    </td>

                    <td>
                        @if($user->is_admin)
                            <span class="adm-badge adm-badge--amber">
                                <i class="bi bi-shield-fill"></i>
                                Admin
                            </span>
                        @else
                            <span class="adm-badge adm-badge--gray">
                                Utilizador
                            </span>
                        @endif
                    </td>

                    <td>
                        <div class="adm-action-btns">
                            @if(!$isCurrentUser)

                                @if(!$user->is_admin)
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.make-admin', $user) }}"
                                        style="margin:0"
                                        data-confirm="Tornar {{ $user->name }} administrador?"
                                    >
                                        @csrf

                                        <button type="submit" class="adm-action-btn adm-action-btn--success">
                                            <i class="bi bi-shield-plus"></i>
                                            Admin
                                        </button>
                                    </form>
                                @else
                                    <form
                                        method="POST"
                                        action="{{ route('admin.users.remove-admin', $user) }}"
                                        style="margin:0"
                                        data-confirm="Remover permissões de admin de {{ $user->name }}?"
                                    >
                                        @csrf

                                        <button type="submit" class="adm-action-btn">
                                            <i class="bi bi-shield-minus"></i>
                                            Remover
                                        </button>
                                    </form>
                                @endif

                                <form
                                    method="POST"
                                    action="{{ route('admin.users.destroy', $user) }}"
                                    style="margin:0"
                                    data-confirm="Apagar o utilizador {{ $user->name }}? Esta ação não pode ser revertida."
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="adm-action-btn adm-action-btn--danger"
                                        title="Apagar utilizador"
                                    >
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>

                            @else
                                <span class="adm-badge adm-badge--blue">
                                    Conta atual
                                </span>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">
                        <div class="adm-empty">
                            Não foram encontrados utilizadores.
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if(method_exists($users, 'links') && $users->hasPages())
        <div class="adm-pagination">
            {{ $users->links() }}
        </div>
    @endif
</div>

@endsection