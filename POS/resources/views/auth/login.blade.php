<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar sesión — Don Pulpo POS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <style>
        :root { --dp-navy: #04152c; --dp-aqua: #09d1d0; }
        body {
            min-height: 100svh;
            display: grid;
            place-items: center;
            background:
                radial-gradient(circle at 10% 10%, rgba(9,209,208,.22), transparent 28rem),
                linear-gradient(180deg, #f8fbff 0%, #eef5f8 100%);
        }
        .login-card {
            width: min(420px, 95vw);
            border: none;
            border-radius: 24px;
            box-shadow: 0 24px 60px rgba(4,21,44,.13);
        }
        .brand-octo { font-size: 56px; line-height: 1; }
        .btn-dp { background: var(--dp-aqua); color: #fff; border: none; min-height: 52px; font-weight: 800; font-size: 17px; border-radius: 14px; }
        .btn-dp:hover { background: #07b8b7; color: #fff; }
        .form-control { border-radius: 12px; min-height: 50px; font-size: 16px; }
        .form-control:focus { border-color: var(--dp-aqua); box-shadow: 0 0 0 .2rem rgba(9,209,208,.2); }
    </style>
</head>
<body>
<div class="login-card card p-4 p-md-5">
    <div class="text-center mb-4">
        <div class="brand-octo">🐙</div>
        <h1 class="h3 fw-bold mt-2" style="color:#04152c">DON PULPO</h1>
        <p class="text-muted mb-0">Punto de Venta</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <form action="{{ route('login.post') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label fw-semibold">Correo electrónico</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                   placeholder="usuario@donpulpo.test" required autofocus />
        </div>
        <div class="mb-4">
            <label class="form-label fw-semibold">Contraseña</label>
            <input type="password" name="password" class="form-control" placeholder="••••••••" required />
        </div>
        <button type="submit" class="btn btn-dp w-100">Ingresar al POS</button>
    </form>
</div>
</body>
</html>
