<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#4F46E5" />
    <meta name="robots" content="noindex, nofollow" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <title>Admin Sign In | Web4rtTech</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" />
</head>
<body>
<div id="root">
    <div class="App">
        <main class="admin-login-page">
            <div class="admin-login-card">
                <div class="admin-login-logo">
                    <span class="admin-login-logo-mark">4</span>
                    <span class="admin-login-logo-copy">
                        <span class="admin-login-logo-text">Web<span>4</span>rtTech</span>
                        <span class="admin-login-logo-tagline">Build · Grow · Automate</span>
                    </span>
                </div>
                <p class="admin-login-subtitle">Admin Sign In</p>

                @if ($errors->any())
                    <div class="admin-login-error">{{ $errors->first() }}</div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}" novalidate id="login-form">
                    @csrf
                    <div class="admin-form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="admin@web4rttech.com" autofocus />
                    </div>

                    <div class="admin-form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required placeholder="••••••••" />
                    </div>

                    <button type="submit" class="admin-login-btn">Sign In</button>
                </form>
            </div>
        </main>
    </div>
</div>
<script>
    document.getElementById('login-form').addEventListener('submit', function () {
        var btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Signing In...';
    });
</script>
</body>
</html>
