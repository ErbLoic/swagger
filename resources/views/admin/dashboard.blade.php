@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>Dashboard</h1>
            <p class="muted">Vue rapide de tes APIs, routes importees, projets perso et derniers appels.</p>
        </div>
        <div class="actions">
            <a class="button" href="{{ route('admin.apis.create') }}">Ajouter une API</a>
            <a class="button secondary" href="{{ route('admin.personal-projects.create') }}">Ajouter un projet perso</a>
            <a class="button secondary" href="{{ route('admin.postman.index') }}">Ouvrir Postman</a>
        </div>
    </div>

    <div class="grid grid-3">
        <div class="panel"><span class="muted">APIs</span><div class="metric">{{ $apiCount }}</div></div>
        <div class="panel"><span class="muted">Routes</span><div class="metric">{{ $routeCount }}</div></div>
        <div class="panel"><span class="muted">Appels</span><div class="metric">{{ $historyCount }}</div></div>
        <div class="panel"><span class="muted">Projets perso</span><div class="metric">{{ $personalProjectCount }}</div></div>
        <div class="panel"><span class="muted">Projets publies</span><div class="metric">{{ $publishedPersonalProjectCount }}</div></div>
        <div class="panel"><span class="muted">Manifest</span><div class="metric mono" style="font-size:16px;">/api/manifest</div></div>
    </div>

    <div class="panel" style="margin-top:18px;">
        <h2>Derniers appels</h2>
        <table>
            <thead><tr><th>Methode</th><th>URL</th><th>Status</th><th>Duree</th></tr></thead>
            <tbody>
            @forelse ($recentHistories as $history)
                <tr>
                    <td><span class="badge">{{ $history->method }}</span></td>
                    <td class="mono">{{ $history->url }}</td>
                    <td>{{ $history->status_code ?? 'Erreur' }}</td>
                    <td>{{ $history->duration_ms }} ms</td>
                </tr>
            @empty
                <tr><td colspan="4" class="muted">Aucun appel pour l'instant.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
