<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Don Pulpo POS')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <style>
        :root { --dp-navy: #04152c; --dp-aqua: #09d1d0; --dp-coral: #ff6048; }
        body { background: #f6f9fc; }
        .navbar { background: var(--dp-navy); }
        .navbar-brand { color: var(--dp-aqua) !important; font-weight: 800; display: inline-flex; align-items: center; gap: 8px; }
        .navbar-brand img { width: 30px; height: 30px; object-fit: contain; }
        .nav-link { color: rgba(255,255,255,.80) !important; }
        .nav-link:hover { color: var(--dp-aqua) !important; }
        .btn-dp { background: var(--dp-aqua); color: #fff; border: none; }
        .btn-dp:hover { background: #07b8b7; color: #fff; }
    </style>
    @yield('styles')
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('home') }}">
            <img src="{{ asset('assets/images/logo.png') }}" alt="Don Pulpo" />
            Don Pulpo POS
        </a>
        <div class="navbar-nav ms-auto flex-row gap-3">
            <span class="nav-link text-white-50 small">{{ Auth::user()->name ?? '' }}</span>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-light">Salir</button>
            </form>
        </div>
    </div>
</nav>

<main class="container-fluid px-4 py-3">
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible mt-2" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @yield('content')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@yield('scripts')
</body>
</html>
