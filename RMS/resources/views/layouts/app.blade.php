<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Don Pulpo RMS')</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <style>
        :root {
            --navy-bg:    #01132E;
            --navy-dark:  #020E1F;
            --navy-deep:  #062645;
            --blue-muted: #2B455C;
            --blue-med:   #4F6C81;
            --blue-light: #A2C3D5;
            --cream:      #EEEFEC;
            --gold:       #CB8317;
            --gold-dark:  #8E601C;
            --gold-100:   #fdf3e3;
            --coral:      #ff6048;
        }
        body { background: #f2f6f9; color: #1a2535; }
        .navbar { background: var(--navy-dark); border-bottom: 1px solid rgba(203,131,23,.18); }
        .navbar-brand { color: var(--gold) !important; font-weight: 800; letter-spacing: .05em; }
        .nav-link { color: rgba(255,255,255,.78) !important; }
        .nav-link:hover, .nav-link.active { color: var(--gold) !important; }
        .btn-dp { background: linear-gradient(135deg,var(--gold),var(--gold-dark)); color: #fff; border: none; font-weight: 700; }
        .btn-dp:hover { background: linear-gradient(135deg,var(--gold-dark),#6e4a14); color: #fff; }
        .btn-dp-outline { border: 1.5px solid var(--gold); color: var(--gold-dark); background: #fff; font-weight: 700; }
        .btn-dp-outline:hover { background: var(--gold-100); color: var(--gold-dark); border-color: var(--gold); }
        .badge-active   { background: #d1fae5; color: #065f46; }
        .badge-inactive { background: #fee2e2; color: #991b1b; }
        .card { border-color: #c8d9e5; }
        .card-header { background: var(--navy-deep); color: #fff; border-bottom: 1px solid rgba(203,131,23,.20); }
        main { padding-top: 1.5rem; }
        .table thead th { background: var(--navy-deep); color: var(--cream); border-color: rgba(162,195,213,.25); font-weight: 700; }
        .table tbody tr:hover { background: var(--gold-100); }
        .form-control:focus, .form-select:focus { border-color: var(--gold); box-shadow: 0 0 0 .2rem rgba(203,131,23,.18); }
        .page-link { color: var(--gold-dark); }
        .page-item.active .page-link { background: var(--gold); border-color: var(--gold); }
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
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shifts*') ? 'active' : '' }}" href="{{ route('shifts.index') }}">Cortes de caja</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('discount-codes*') ? 'active' : '' }}" href="{{ route('discount-codes.index') }}">Descuentos</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('petty-cash*') ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown">
                        Caja chica
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item {{ request()->routeIs('petty-cash.vouchers*') ? 'active' : '' }}"
                               href="{{ route('petty-cash.vouchers.index') }}">Vales</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('petty-cash.categories*') ? 'active' : '' }}"
                               href="{{ route('petty-cash.categories.index') }}">Categorías</a></li>
                    </ul>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto align-items-center gap-2">
                <li class="nav-item">
                    <span class="nav-link pe-none" style="color:rgba(255,255,255,.55)!important;font-size:.85rem">
                        {{ Auth::user()->name }}
                        <span class="badge ms-1" style="background:rgba(203,131,23,.22);color:var(--gold);font-size:.7rem">
                            {{ Auth::user()->getRoleLabel() }}
                        </span>
                    </span>
                </li>
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm rounded-3"
                                style="font-size:.8rem;border:1px solid rgba(203,131,23,.35);color:rgba(255,255,255,.7);background:transparent">
                            Cerrar sesión
                        </button>
                    </form>
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
