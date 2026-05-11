@extends('layouts.app')

@php
    $input = $oldInput ?? [];
    $currentProject = old('api_project_id', $input['api_project_id'] ?? $selectedProject?->id);
    $currentRoute = old('api_route_id', $input['api_route_id'] ?? $selectedRoute?->id);
    $currentMethod = old('method', $input['method'] ?? $selectedRoute?->method ?? 'GET');
    $currentPath = old('path', $input['path'] ?? $selectedRoute?->uri ?? '');
@endphp

@section('content')
    <div class="topbar">
        <div>
            <h1>Postman interne</h1>
            <p class="muted">Teste les routes enregistrées et conserve un historique des appels.</p>
        </div>
    </div>

    <div class="postman-grid">
        <aside class="panel route-list">
            <h2>Routes disponibles</h2>
            <div class="field" style="margin-top:14px;">
                <label for="route_picker">Sélection rapide</label>
                <select id="route_picker" data-route-select>
                    <option value="">Choisir une route</option>
                    @foreach ($projects as $project)
                        <optgroup label="{{ $project->name }}">
                            @foreach ($project->routes as $route)
                                <option
                                    value="{{ $route->id }}"
                                    data-project-id="{{ $project->id }}"
                                    data-method="{{ $route->method }}"
                                    data-uri="{{ $route->uri }}"
                                    @selected((int) $currentRoute === $route->id)
                                >
                                    {{ $route->method }} {{ $route->uri }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
            </div>

            <h3>Historique</h3>
            @forelse ($histories as $history)
                <button
                    type="button"
                    class="route-choice"
                    data-history
                    data-project-id="{{ $history->api_project_id }}"
                    data-route-id="{{ $history->api_route_id }}"
                    data-method="{{ $history->method }}"
                    data-url="{{ $history->url }}"
                    data-body="{{ e($history->request_body) }}"
                    style="background:transparent;border:0;text-align:left;width:100%;"
                >
                    <span class="badge">{{ $history->method }}</span>
                    <span class="mono">{{ $history->status_code ?? 'ERR' }}</span>
                    <div class="muted mono">{{ $history->url }}</div>
                </button>
            @empty
                <p class="muted">Aucun historique.</p>
            @endforelse
        </aside>

        <section class="grid">
            <form class="panel" method="POST" action="{{ route('admin.postman.send') }}">
                @csrf
                <input type="hidden" name="api_route_id" value="{{ $currentRoute }}">

                <div class="grid grid-2">
                    <div class="field">
                        <label for="api_project_id">API</label>
                        <select id="api_project_id" name="api_project_id" required>
                            <option value="">Choisir</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" @selected((int) $currentProject === $project->id)>{{ $project->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field">
                        <label for="method">Méthode</label>
                        <select id="method" name="method">
                            @foreach (['GET', 'POST', 'PUT', 'PATCH', 'DELETE'] as $method)
                                <option value="{{ $method }}" @selected($currentMethod === $method)>{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label for="path">URL ou chemin</label>
                    <input id="path" name="path" class="mono" value="{{ $currentPath }}" placeholder="/api/users" required>
                </div>

                <div class="grid grid-2">
                    <div class="field">
                        <label for="headers">Headers JSON ou clé: valeur</label>
                        <textarea id="headers" name="headers" class="mono">{{ old('headers', $input['headers'] ?? '') }}</textarea>
                    </div>
                    <div class="field">
                        <label for="query_params">Query params JSON ou clé: valeur</label>
                        <textarea id="query_params" name="query_params" class="mono">{{ old('query_params', $input['query_params'] ?? '') }}</textarea>
                    </div>
                </div>

                <div class="field">
                    <label for="body_type">Type de body</label>
                    <select id="body_type" name="body_type">
                        @foreach (['json' => 'JSON', 'form' => 'Form', 'raw' => 'Raw'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('body_type', $input['body_type'] ?? 'json') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="body">Body</label>
                    <textarea id="body" name="body" class="mono">{{ old('body', $input['body'] ?? '') }}</textarea>
                </div>

                <button class="button" type="submit">Envoyer</button>
            </form>

            @if ($result)
                <div class="panel">
                    <h2>Réponse</h2>
                    <div class="response-meta">
                        <span class="badge {{ $result['status'] && $result['status'] < 400 ? 'ok' : 'warn' }}">Status {{ $result['status'] ?? 'Erreur' }}</span>
                        <span class="badge">{{ $result['duration'] }} ms</span>
                    </div>
                    @if ($result['error'])
                        <div class="alert error">{{ $result['error'] }}</div>
                    @endif
                    <h3>Body</h3>
                    <pre>{{ $result['body'] }}</pre>
                    <h3>Headers</h3>
                    <pre>{{ json_encode($result['headers'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            @endif
        </section>
    </div>
@endsection
