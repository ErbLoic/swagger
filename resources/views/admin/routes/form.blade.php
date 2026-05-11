@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>{{ $route->exists ? 'Modifier la route' : 'Nouvelle route' }}</h1>
            <p class="muted">{{ $project->name }}</p>
        </div>
        <a class="button secondary" href="{{ route('admin.apis.show', $project) }}">Retour</a>
    </div>

    <form class="panel" method="POST" action="{{ $route->exists ? route('admin.apis.routes.update', [$project, $route]) : route('admin.apis.routes.store', $project) }}">
        @csrf
        @if ($route->exists)
            @method('PUT')
        @endif

        <div class="grid grid-2">
            <div class="field">
                <label for="method">Méthode</label>
                <select id="method" name="method">
                    @foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method)
                        <option value="{{ $method }}" @selected(old('method', $route->method) === $method)>{{ $method }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field">
                <label for="uri">URI</label>
                <input id="uri" name="uri" value="{{ old('uri', $route->uri) }}" required>
            </div>
        </div>

        <div class="grid grid-2">
            <div class="field">
                <label for="name">Nom</label>
                <input id="name" name="name" value="{{ old('name', $route->name) }}">
            </div>
            <div class="field">
                <label for="action">Action</label>
                <input id="action" name="action" value="{{ old('action', $route->action) }}">
            </div>
        </div>

        <div class="grid grid-2">
            <div class="field">
                <label for="middleware">Middleware, un par ligne</label>
                <textarea id="middleware" name="middleware">{{ old('middleware', implode("\n", $route->middleware ?? [])) }}</textarea>
            </div>
            <div class="field">
                <label for="parameters">Paramètres JSON ou un par ligne</label>
                <textarea id="parameters" name="parameters">{{ old('parameters', json_encode($route->parameters ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
            </div>
        </div>

        <div class="grid grid-2">
            <div class="field">
                <label for="headers">Headers suggérés JSON ou clé: valeur</label>
                <textarea id="headers" name="headers">{{ old('headers', json_encode($route->headers ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
            </div>
            <div class="field">
                <label for="body_schema">Body attendu JSON</label>
                <textarea id="body_schema" name="body_schema">{{ old('body_schema', json_encode($route->body_schema ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) }}</textarea>
            </div>
        </div>

        <button class="button" type="submit">Enregistrer</button>
    </form>
@endsection
