<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Iniciar sesión — Don Pulpo RMS</title>
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
            min-height: 100vh;
            background: radial-gradient(circle at 12% 10%, rgba(203,131,23,.12), transparent 28rem),
                        radial-gradient(circle at 88% 5%, rgba(6,38,69,.18), transparent 22rem),
                        linear-gradient(180deg, #f2f6f9 0%, #e8eef5 100%);
            display: flex; align-items: center; justify-content: center;
            font-family: Inter, system-ui, sans-serif;
        }
        .login-card {
            width: min(420px, 94vw);
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
        .octo { font-size: 3rem; line-height: 1; }
        .login-header h1 { color: var(--gold); font-size: 1.6rem; font-weight: 800; letter-spacing: .06em; margin: .5rem 0 .25rem; }
        .login-header p  { color: rgba(255,255,255,.55); font-size: .85rem; font-weight: 600; letter-spacing: .1em; text-transform: uppercase; margin: 0; }
        .login-body { padding: 2rem; }
        .form-label { font-weight: 700; font-size: .9rem; color: #2B455C; }
        .form-control {
            border-radius: 12px; border: 1.5px solid #c8d9e5;
            padding: .75rem 1rem; font-size: 1rem;
        }
        .form-control:focus { border-color: var(--gold); box-shadow: 0 0 0 3px rgba(203,131,23,.15); }
        .btn-login {
            width: 100%; padding: .85rem; border-radius: 14px; border: none;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            color: #fff; font-weight: 800; font-size: 1rem; letter-spacing: .02em;
            cursor: pointer; transition: opacity .15s;
        }
        .btn-login:hover { opacity: .92; }
        .is-invalid { border-color: #ff6048 !important; }
        .invalid-feedback { color: #ff6048; font-size: .85rem; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <div class="octo">🐙</div>
            <h1>DON PULPO</h1>
            <p>Sistema de Administración</p>
        </div>
        <div class="login-body">
            @if($errors->any())
                <div class="alert alert-danger rounded-3 mb-4" style="font-size:.9rem">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input id="email" type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" autocomplete="email" autofocus
                           placeholder="admin@donpulpo.test" />
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Contraseña</label>
                    <input id="password" type="password" name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           autocomplete="current-password" placeholder="••••••••" />
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember"
                           style="accent-color:var(--gold)" />
                    <label class="form-check-label" for="remember" style="font-size:.9rem">Recordar sesión</label>
                </div>
                <button type="submit" class="btn-login">Iniciar sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
