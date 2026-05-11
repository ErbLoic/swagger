<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'API Console') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bg-body-tertiary">
    @auth
        <div class="app-shell min-vh-100">
            <aside class="sidebar">
                <div class="brand d-flex align-items-center gap-2">
                    <span class="brand-mark">API</span>
                    <span>Console</span>
                </div>
                <nav class="nav flex-column gap-2">
                    <a href="{{ route('admin.dashboard') }}" @class(['nav-link', 'active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                    <a href="{{ route('admin.apis.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.apis.*')])>APIs</a>
                    <a href="{{ route('admin.personal-projects.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.personal-projects.*')])>Projets perso</a>
                    <a href="{{ route('admin.postman.index') }}" @class(['nav-link', 'active' => request()->routeIs('admin.postman.*')])>Postman interne</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout-button" type="submit">Déconnexion</button>
                    </form>
                </nav>
            </aside>
            <main class="content container-fluid">
                <div class="content-inner">
                    @include('partials.flash')
                    @yield('content')
                </div>
            </main>
        </div>
    @else
        @yield('content')
    @endauth
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
