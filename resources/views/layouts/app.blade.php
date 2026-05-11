<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'API Console') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    @auth
        <div class="app-shell">
            <aside class="sidebar">
                <div class="brand">API Console</div>
                <nav class="nav">
                    <a href="{{ route('admin.dashboard') }}" @class(['active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                    <a href="{{ route('admin.apis.index') }}" @class(['active' => request()->routeIs('admin.apis.*')])>APIs</a>
                    <a href="{{ route('admin.personal-projects.index') }}" @class(['active' => request()->routeIs('admin.personal-projects.*')])>Projets perso</a>
                    <a href="{{ route('admin.postman.index') }}" @class(['active' => request()->routeIs('admin.postman.*')])>Postman interne</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout-button" type="submit">Déconnexion</button>
                    </form>
                </nav>
            </aside>
            <main class="content">
                @include('partials.flash')
                @yield('content')
            </main>
        </div>
    @else
        @yield('content')
    @endauth
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
