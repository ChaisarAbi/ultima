<!-- Standalone Login Page - No Layout -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login — BMW ULTIMA</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --accent: #2563eb;
            --accent-light: #3b82f6;
            --radius-sm: 8px;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            color: #1e293b;
            font-size: .875rem;
            line-height: 1.6;
            position: relative;
            overflow: hidden;
        }

        /* Logo watermark background */
        body::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 500px;
            height: 500px;
            background-image: url('{{ asset("logo.webp") }}');
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            opacity: 0.06;
            z-index: 0;
            pointer-events: none;
        }

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .login-logo {
            text-align: center;
            margin-bottom: 2rem;
        }

        .login-logo img {
            height: 80px;
            width: auto;
            object-fit: contain;
            margin-bottom: 1rem;
            filter: drop-shadow(0 4px 12px rgba(0,0,0,0.3));
        }

        .login-logo h2 {
            color: #fff;
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: -.02em;
            margin: 0;
            text-shadow: 0 2px 8px rgba(0,0,0,0.3);
        }

        .login-logo p {
            color: rgba(255,255,255,0.6);
            font-size: .8125rem;
            margin-top: .5rem;
        }

        .login-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3), 0 0 0 1px rgba(255,255,255,0.05);
            background: rgba(255,255,255,0.98);
            backdrop-filter: blur(20px);
            overflow: hidden;
        }

        .login-card-header {
            background: linear-gradient(135deg, var(--accent), #1d4ed8);
            padding: 1.5rem;
            text-align: center;
        }

        .login-card-header h4 {
            color: #fff;
            margin: 0;
            font-weight: 700;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .login-card-body {
            padding: 2rem;
        }

        .form-control {
            border-radius: var(--radius-sm);
            border: 1px solid #e2e8f0;
            font-size: .875rem;
            padding: .6rem 1rem;
            color: #1e293b;
            transition: border-color .15s ease, box-shadow .15s ease;
        }

        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
        }

        .form-label {
            font-weight: 600;
            font-size: .8125rem;
            color: #334155;
            margin-bottom: .35rem;
        }

        .btn-login {
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: .875rem;
            padding: .6rem 1.5rem;
            background: var(--accent);
            border-color: var(--accent);
            transition: all .15s ease;
        }

        .btn-login:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }

        .invalid-feedback {
            font-size: .75rem;
        }

        @media (max-width: 576px) {
            .login-logo img {
                height: 60px;
            }
            .login-card-body {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        {{-- Logo Section --}}
        <div class="login-logo">
            <img src="{{ asset('logo.webp') }}" alt="BMW ULTIMA Logo">
            <h2>BMW ULTIMA</h2>
            <p>Sistem Monitoring Layanan Servis Kendaraan</p>
        </div>

        {{-- Login Card --}}
        <div class="card login-card">
            <div class="login-card-header">
                <h4><i class="bi bi-tools"></i> Login</h4>
            </div>
            <div class="login-card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus autocomplete="username">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" id="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="current-password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" name="remember" id="remember" class="form-check-input" id="remember">
                        <label for="remember" class="form-check-label">Remember me</label>
                    </div>

                    <button type="submit" class="btn btn-primary btn-login w-100">
                        <i class="bi bi-box-arrow-in-right me-2"></i>Login
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>