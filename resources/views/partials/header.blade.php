@php
    $navLink = fn ($path) => ($path === '/' ? request()->is('/') : request()->is(ltrim($path, '/'), ltrim($path, '/').'/*')) ? 'active-link' : '';
@endphp
<header class="header">
    <div class="container header-container">
        <a href="{{ url('/') }}" class="logo">
            <span class="logo-mark">4</span>
            <span class="logo-copy">
                <span class="logo-text">Web<span class="logo-accent">4</span>rtTech</span>
                <span class="logo-tagline">Build · Grow · Automate</span>
            </span>
        </a>

        <nav class="nav" id="site-nav" aria-label="Primary Navigation">
            <a href="{{ url('/') }}" class="{{ $navLink('/') }}">Home</a>
            <a href="{{ url('/about') }}" class="{{ $navLink('/about') }}">About</a>
            <a href="{{ url('/services') }}" class="{{ $navLink('/services') }}">Services</a>
            <a href="{{ url('/ventures') }}" class="{{ $navLink('/ventures') }}">Ventures</a>
            <a href="{{ url('/careers') }}" class="{{ $navLink('/careers') }}">Careers</a>
            <a href="{{ url('/contact') }}" class="{{ $navLink('/contact') }}">Contact</a>
            <a href="{{ url('/contact') }}" class="nav-cta nav-cta-mobile">Get a Quote</a>
        </nav>

        <div class="header-actions">
            <a href="{{ url('/contact') }}" class="nav-cta">Get a Quote</a>
        </div>

        <button class="menu-toggle" id="menu-toggle" aria-label="Toggle navigation menu" aria-expanded="false">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <div class="nav-backdrop" id="nav-backdrop" hidden></div>
</header>
