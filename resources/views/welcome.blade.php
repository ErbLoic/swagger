@extends('layouts.app')

@section('content')
    <div class="auth-page">
        <div class="auth-card text-center">
            <h1>API Console</h1>
            <p class="muted">Interface interne de gestion et de test des APIs.</p>
            <a class="button" href="{{ route('admin.dashboard') }}">Ouvrir le dashboard</a>
        </div>
    </div>
@endsection
