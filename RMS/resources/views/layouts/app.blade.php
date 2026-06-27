<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Don Pulpo RMS')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <style>
        :root { --dp-navy: #04152c; --dp-aqua: #09d1d0; --dp-coral: #ff6048; }
        body { background: #f6f9fc; }
        .navbar { background: var(--dp-navy); }
        .navbar-brand { color: var(--dp-aqua) !important; font-weight: 800; letter-spacing: .05em; }
        .nav-link { color: rgba(255,255,255,.80) !important; }
        .nav-link:hover, .nav-link.active { color: var(--dp-aqua) !important; }
        .btn-dp { background: var(--dp-aqua); color: #fff; border: none; }
        .btn-dp:hover { background: #07b8b7; color: #fff; }
        .badge-active   { background: #d1fae5; color: #065f46; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .badge-tmp      { background: #fef3c7; color: #92400e; }
        main { padding-top: 1.5rem; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">🐙 Don Pulpo RMS</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
            <span class="navbar-toggler-icon" style="filter:invert(1)"></span>
        </button>
        <div class="collapse navbar-collapse" id="navMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('users*') ? 'active' : '' }}" href="{{ route('users.index') }}">Usuarios</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dish-categories*') ? 'active' : '' }}" href="{{ route('dish-categories.index') }}">Categorías</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('dishes*') ? 'active' : '' }}" href="{{ route('dishes.index') }}">Platillos</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<main class="container-fluid px-4">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
