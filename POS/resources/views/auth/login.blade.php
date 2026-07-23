<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar sesión — Don Pulpo POS</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" />
    <style>
        :root {
            --navy-dark: #020E1F;
            --navy-deep: #062645;
            --gold:      #CB8317;
            --gold-dark: #8E601C;
            --gold-100:  #fdf3e3;
        }
        body {
            min-height: 100svh;
            display: grid; place-items: center;
            background: radial-gradient(circle at 12% 10%, rgba(203,131,23,.12), transparent 28rem),
                        radial-gradient(circle at 88% 5%, rgba(6,38,69,.18), transparent 22rem),
                        linear-gradient(180deg, #f2f6f9 0%, #e8eef5 100%);
            font-family: Inter, system-ui, sans-serif;
        }
        .login-card {
            width: min(420px, 95vw);
            background: #fff;
            border-radius: 28px;
            box-shadow: 0 24px 60px rgba(1,19,46,.14);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, var(--navy-dark), var(--navy-deep));
            padding: 2rem 2rem 1.75rem;
            text-align: center;
            border-bottom: 2px solid rgba(203,131,23,.25);
        }
        .octo { width: 76px; height: 76px; object-fit: contain; margin: 0 auto; display: block; }
        .login-header h1 { color: var(--gold); font-size: 1.6rem; font-weight: 800; letter-spacing: .06em; margin: .5rem 0 .25rem; }
        .login-header p  { color: rgba(255,255,255,.55); font-size: .85rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; margin: 0; }
        .login-body { padding: 2rem; }
        .form-label { font-weight: 700; font-size: .9rem; color: #2B455C; }
        .form-control { border-radius: 12px; border: 1.5px solid #c8d9e5; padding: .75rem 1rem; font-size: 1rem; min-height: 50px; }
        .form-control:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(203,131,23,.15); }
        .btn-login {
            width: 100%; padding: .85rem; border-radius: 14px; border: none;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #fff; font-weight: 800; font-size: 1rem;
            cursor: pointer; transition: opacity .15s;
        }
        .btn-login:hover { opacity: .90; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <img class="octo" src="{{ asset('assets/images/logo.png') }}" alt="Don Pulpo" />
            <h1>DON PULPO</h1>
            <p>Punto de Venta</p>
        </div>
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4" style="font-size:.9rem">
                    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger rounded-3 mb-4" style="font-size:.9rem">{{ session('error') }}</div>
            @endif

            <form action="{{ route('login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email') }}" placeholder="usuario@donpulpo.test"
                           required autofocus autocomplete="email" />
                </div>
                <div class="mb-4">
                    <label class="form-label">Contraseña</label>
                    <input type="password" name="password" class="form-control"
                           placeholder="••••••••" required autocomplete="current-password" />
                </div>
                <button type="submit" class="btn-login">Ingresar al POS</button>
            </form>
        </div>
    </div>
</body>
</html>
