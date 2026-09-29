@extends('layouts.site', [
    'title' => 'Web Development, Digital Marketing & AI Solutions',
    'description' => 'Web4rtTech is a digital-first IT services and consulting company offering web & mobile development, SEO, PPC, digital marketing, and AI & data science solutions for businesses in India and worldwide.',
    'path' => '/',
])

@php
    $slides = [
        ['id' => 'development', 'eyebrow' => 'Web & Mobile Development', 'title' => "Building Tomorrow's Technology, Today", 'description' => 'We design, build, and scale websites, apps, and cloud platforms that turn visitors into customers.', 'ctaLabel' => 'Explore Development', 'ctaTo' => '/services#development'],
        ['id' => 'growth', 'eyebrow' => 'Digital Marketing & Growth', 'title' => 'Marketing That Gets You Found — and Chosen', 'description' => 'SEO, paid ads, and social strategy engineered around measurable growth, not vanity metrics.', 'ctaLabel' => 'Explore Growth Marketing', 'ctaTo' => '/services#growth'],
        ['id' => 'ai-data', 'eyebrow' => 'AI & Data Science', 'title' => 'Turn Data Into Decisions, Automatically', 'description' => 'Practical AI and analytics that automate the busywork and surface the insights that actually matter.', 'ctaLabel' => 'Explore AI & Data', 'ctaTo' => '/services#ai-data'],
    ];

    $why = [
        ['title' => 'One Team, Every Channel', 'description' => 'Development and growth marketing under one roof — your site, app, SEO, and ads all work toward the same goal.'],
        ['title' => 'Built for Global Clients', 'description' => 'Comfortable working across time zones and currencies, with clear async communication and transparent reporting.'],
        ['title' => 'Data-Backed Decisions', 'description' => 'Every campaign and product decision is measured — you always know what’s working and why.'],
        ['title' => 'Direct Access, No Layers', 'description' => 'You work directly with the people building your product and running your campaigns, not account managers relaying messages.'],
    ];

    $steps = [
        ['title' => 'Discover', 'description' => 'We start with a conversation about your goals, audience, and constraints — no cookie-cutter proposals.'],
        ['title' => 'Plan & Design', 'description' => 'A clear scope, timeline, and design direction before any development or campaign work begins.'],
        ['title' => 'Build & Execute', 'description' => 'Iterative delivery with regular check-ins, so you always know exactly where things stand.'],
        ['title' => 'Launch & Grow', 'description' => 'We don’t disappear after launch — ongoing support and optimization keep results compounding.'],
    ];

    $featured = array_map(fn ($id) => \App\Support\Site::service($id), ['web-development', 'mobile-app-development', 'seo', 'ppc-google-ads', 'ai-solutions', 'data-science']);
@endphp

@section('content')
<main>
    {{-- Hero Slider --}}
    <section class="hero-slider" data-hero-slider>
        <div class="hero-slider-track">
            @foreach ($slides as $i => $slide)
                <div class="hero-slide slide-{{ $slide['id'] }} {{ $i === 0 ? 'active' : '' }}" aria-hidden="{{ $i === 0 ? 'false' : 'true' }}">
                    <div class="hero-slide-content">
                        <span class="hero-slide-eyebrow">{{ $slide['eyebrow'] }}</span>
                        <h1>{{ $slide['title'] }}</h1>
                        <p>{{ $slide['description'] }}</p>
                        <div class="hero-slide-actions">
                            <a href="{{ url($slide['ctaTo']) }}" class="hero-slide-cta">{{ $slide['ctaLabel'] }}</a>
                            <a href="{{ url('/contact') }}" class="hero-slide-secondary">Get a Quote →</a>
                        </div>
                    </div>
                    @include('partials.hero-illustration', ['variant' => $slide['id']])
                </div>
            @endforeach
        </div>

        <button class="hero-slider-arrow prev" data-slide-prev aria-label="Previous slide">‹</button>
        <button class="hero-slider-arrow next" data-slide-next aria-label="Next slide">›</button>

        <div class="hero-slider-dots">
            @foreach ($slides as $i => $slide)
                <button class="hero-slider-dot {{ $i === 0 ? 'active' : '' }}" data-slide-to="{{ $i }}" aria-label="Go to {{ $slide['eyebrow'] }} slide"></button>
            @endforeach
        </div>
    </section>

    {{-- Purpose Section --}}
    <section class="purpose-section">
        <div class="container">
            <div class="purpose-content">
                <h2>Our Purpose</h2>
                <p>To amplify human potential and unlock the next opportunity for people, businesses and communities through thoughtful technology</p>
            </div>
        </div>
    </section>

    {{-- Why Web4rtTech Section --}}
    <section class="why-section">
        <div class="container">
            <h2>Why Web4rtTech</h2>
            <div class="why-grid">
                @foreach ($why as $item)
                    <div class="why-card">
                        <h3>{{ $item['title'] }}</h3>
                        <p>{{ $item['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Services Section --}}
    <section class="services-section">
        <div class="container">
            <h2>Our Services</h2>
            <div class="services-grid">
                @foreach ($featured as $service)
                    <a href="{{ url('/services/'.$service['id']) }}" class="service-card">
                        <div class="service-icon">
                            @include('partials.service-icon', ['id' => $service['id']])
                        </div>
                        <h3>{{ $service['title'] }}</h3>
                        <p>{{ $service['description'] }}</p>
                    </a>
                @endforeach
            </div>
            <div class="services-view-all">
                <a href="{{ url('/services') }}" class="services-view-all-link">View All Services →</a>
            </div>
        </div>
    </section>

    {{-- Ventures Section --}}
    <section class="ventures-section">
        <div class="container">
            <h2>Beyond Technology</h2>
            <p class="ventures-section-intro">
                The Web4rtTech family also runs homestays in Uttarakhand, a real estate business, and yoga &amp; wellness
                programmes, each with its own website.
            </p>
            @include('partials.venture-grid')
            <div class="services-view-all">
                <a href="{{ url('/ventures') }}" class="services-view-all-link">Explore Our Ventures →</a>
            </div>
        </div>
    </section>

    {{-- Process Section --}}
    <section class="process-section">
        <div class="container">
            <h2>How We Work</h2>
            <p class="process-intro">A straightforward engagement process, whether you're building a product or running a campaign.</p>
            <div class="process-grid">
                @foreach ($steps as $i => $step)
                    <div class="process-step">
                        <div class="process-step-number">{{ $i + 1 }}</div>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- News Section --}}
    <section class="news-section">
        <div class="container">
            <h2>Latest Updates</h2>
            <div class="news-grid">
                <article class="news-card">
                    <h3>Web4rtTech Launches AI-First Delivery Framework</h3>
                    <p>A new approach to project delivery that embeds AI-assisted engineering across every stage</p>
                    <a href="{{ url('/news/ai-first-delivery') }}" class="read-more">Read more →</a>
                </article>
                <article class="news-card">
                    <h3>Web4rtTech Cloud Fabric™ Now Live</h3>
                    <p>A composable stack of cloud services and connectors built to accelerate enterprise migrations</p>
                    <a href="{{ url('/news/cloud-fabric') }}" class="read-more">Read more →</a>
                </article>
                <article class="news-card">
                    <h3>New Innovations in Tech</h3>
                    <p>Discover how we're driving innovation and digital excellence for growing enterprises</p>
                    <a href="{{ url('/news/innovations-2025') }}" class="read-more">Read more →</a>
                </article>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="cta-section">
        <div class="container">
            <h2>Let's build what's next, together</h2>
            <p>Connect with our experts to discover how Web4rtTech can transform your business</p>
            <a href="{{ url('/contact') }}">
                <button class="cta-button-primary">Contact Us</button>
            </a>
        </div>
    </section>
</main>
@endsection
