<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#4F46E5" />
    <meta name="robots" content="noindex, nofollow" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <title>Admin Sign In | Web4rtTech</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" />
</head>
<body class="admin-body">
<div id="root">
    <div class="App">
        <main class="admin-login-page">
            <section class="admin-login-brand" aria-hidden="true">
                <div class="admin-login-logo admin-login-logo-light">
                    <span class="admin-login-logo-mark">4</span>
                    <span class="admin-login-logo-copy">
                        <span class="admin-login-logo-text">Web<span>4</span>rtTech</span>
                        <span class="admin-login-logo-tagline">Build · Grow · Automate</span>
                    </span>
                </div>
                <div class="admin-login-pitch">
                    <h2>Run the whole business from one place.</h2>
                    <p>Leads, projects, invoices, renewals and your team, in one workspace.</p>
                    <ul>
                        <li>@include('admin.partials.icon', ['name' => 'inbox', 'class' => '']) Track every lead from first message to won deal</li>
                        <li>@include('admin.partials.icon', ['name' => 'receipt', 'class' => '']) GST invoices, payments and overdue reminders</li>
                        <li>@include('admin.partials.icon', ['name' => 'trend', 'class' => '']) Live revenue, expenses and profit margins</li>
                    </ul>
                </div>
                <p class="admin-login-foot">© {{ date('Y') }} Web4rtTech</p>
            </section>

            <section class="admin-login-form-side">
                <div class="admin-login-card">
                    <div class="admin-login-logo admin-login-logo-mobile">
                        <span class="admin-login-logo-mark">4</span>
                        <span class="admin-login-logo-text">Web<span>4</span>rtTech</span>
                    </div>
                    <h1>Welcome back</h1>
                    <p class="admin-login-subtitle">Sign in to the Web4rtTech admin panel.</p>

                    @if ($errors->any())
                        <div class="admin-login-error">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.login') }}" novalidate id="login-form">
                        @csrf
                        <div class="admin-form-group">
                            <label for="email">Email</label>
                            <div class="admin-input-icon">
                                @include('admin.partials.icon', ['name' => 'mail', 'class' => ''])
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required placeholder="admin@web4rttech.com" autofocus />
                            </div>
                        </div>

                        <div class="admin-form-group">
                            <label for="password">Password</label>
                            <div class="admin-input-icon">
                                @include('admin.partials.icon', ['name' => 'lock', 'class' => ''])
                                <input type="password" id="password" name="password" required placeholder="••••••••" />
                            </div>
                        </div>

                        <button type="submit" class="admin-login-btn">Sign In</button>
                    </form>
                </div>
            </section>
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
