@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>{{ $project->name }}</h1>
            <p class="muted mono">{{ $project->base_url }}</p>
        </div>
        <div class="actions">
            <form method="POST" action="{{ route('admin.apis.import', $project) }}">
                @csrf
                <button class="button" type="submit">Synchroniser</button>
            </form>
            <a class="button secondary" href="{{ route('admin.apis.routes.create', $project) }}">Ajouter une route</a>
            <a class="button secondary" href="{{ route('admin.apis.edit', $project) }}">Modifier</a>
            <form method="POST" action="{{ route('admin.apis.destroy', $project) }}" onsubmit="return confirm('Supprimer cette API ?')">
                @csrf
                @method('DELETE')
                <button class="button danger" type="submit">Supprimer</button>
            </form>
        </div>
    </div>

    <div class="panel">
        <h2>Routes</h2>
        <table>
            <thead><tr><th>Méthode</th><th>URI</th><th>Nom</th><th>Action</th><th>Actions</th></tr></thead>
            <tbody>
            @forelse ($project->routes as $route)
                <tr>
                    <td><span class="badge">{{ $route->method }}</span></td>
                    <td class="mono">{{ $route->uri }}</td>
                    <td>{{ $route->name }}</td>
                    <td>{{ $route->action }}</td>
                    <td class="actions">
                        <a class="button secondary" href="{{ route('admin.postman.index', ['route' => $route->id]) }}">Tester</a>
                        <a class="button secondary" href="{{ route('admin.apis.routes.edit', [$project, $route]) }}">Modifier</a>
                        <form method="POST" action="{{ route('admin.apis.routes.destroy', [$project, $route]) }}" onsubmit="return confirm('Supprimer cette route ?')">
                            @csrf
                            @method('DELETE')
                            <button class="button danger" type="submit">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="muted">Aucune route. Synchronise le manifest ou ajoute une route manuellement.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
