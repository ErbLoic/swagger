@extends('layouts.app')

@section('content')
    <div class="topbar">
        <div>
            <h1>{{ $project->exists ? 'Modifier l’API' : 'Nouvelle API' }}</h1>
            <p class="muted">Le manifest doit exposer un JSON contenant un tableau <code>routes</code>.</p>
        </div>
        <a class="button secondary" href="{{ $project->exists ? route('admin.apis.show', $project) : route('admin.apis.index') }}">Retour</a>
    </div>

    <form class="panel" method="POST" action="{{ $project->exists ? route('admin.apis.update', $project) : route('admin.apis.store') }}">
        @csrf
        @if ($project->exists)
            @method('PUT')
        @endif

        <div class="grid grid-2">
            <div class="field">
                <label for="name">Nom</label>
                <input id="name" name="name" value="{{ old('name', $project->name) }}" required>
            </div>
            <div class="field">
                <label for="base_url">Base URL</label>
                <input id="base_url" name="base_url" type="url" value="{{ old('base_url', $project->base_url) }}" placeholder="https://api.example.test" required>
            </div>
        </div>

        <div class="field">
            <label for="manifest_url">URL manifest</label>
            <input id="manifest_url" name="manifest_url" type="url" value="{{ old('manifest_url', $project->manifest_url) }}" placeholder="https://api.example.test/api/manifest" required>
        </div>

        <div class="field">
            <label for="description">Description</label>
            <textarea id="description" name="description">{{ old('description', $project->description) }}</textarea>
        </div>

        <label>
            <input name="is_active" type="checkbox" value="1" style="width:auto; min-height:auto;" @checked(old('is_active', $project->is_active))>
            API active
        </label>

        <div class="actions" style="margin-top:18px;">
            <button class="button" type="submit">Enregistrer</button>
        </div>
    </form>
@endsection
