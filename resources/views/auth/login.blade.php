@extends('layouts.app')

@section('content')
    <div class="auth-page">
        <form class="auth-card" method="POST" action="{{ route('login.store') }}">
            @csrf
            <h1>Connexion admin</h1>
            <p class="muted">Accède au panneau de gestion des APIs et au Postman interne.</p>
            @include('partials.flash')
            <div class="field">
                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="field">
                <label for="password">Mot de passe</label>
                <input id="password" name="password" type="password" required>
            </div>
            <label>
                <input name="remember" type="checkbox" value="1" style="width:auto; min-height:auto;">
                Se souvenir de moi
            </label>
            <div style="margin-top:18px;">
                <button class="button" type="submit">Se connecter</button>
            </div>
        </form>
    </div>
@endsection
