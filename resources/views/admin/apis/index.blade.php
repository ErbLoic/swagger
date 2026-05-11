@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>APIs</h1>
            <p class="muted">Enregistre les APIs Laravel cibles et synchronise leurs routes.</p>
        </div>
        <a class="button" href="{{ route('admin.apis.create') }}">Nouvelle API</a>
    </div>

    <div class="table-panel">
        <table>
            <thead>
                <tr><th>Nom</th><th>Base URL</th><th>Routes</th><th>Statut</th><th>Actions</th></tr>
            </thead>
            <tbody>
            @forelse ($projects as $project)
                <tr>
                    <td><strong>{{ $project->name }}</strong><br><span class="muted">{{ $project->description }}</span></td>
                    <td class="mono">{{ $project->base_url }}</td>
                    <td>{{ $project->routes_count }}</td>
                    <td><span @class(['badge', 'ok' => $project->is_active, 'warn' => ! $project->is_active])>{{ $project->is_active ? 'Actif' : 'Inactif' }}</span></td>
                    <td class="actions">
                        <a class="button secondary" href="{{ route('admin.apis.show', $project) }}">Ouvrir</a>
                        <a class="button secondary" href="{{ route('admin.apis.edit', $project) }}">Modifier</a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Aucune API enregistrée.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    {{ $projects->links() }}
@endsection
