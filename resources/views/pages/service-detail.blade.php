@php
    use App\Support\Site;
    $url = Site::URL;
    $jsonLd = [
        [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => "$url/"],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Services', 'item' => "$url/services"],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $service['title'], 'item' => "$url/services/{$service['id']}"],
            ],
        ],
        [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'serviceType' => $service['title'],
            'name' => $service['title'],
            'description' => $service['long'],
            'url' => "$url/services/{$service['id']}",
            'areaServed' => 'Worldwide',
            'provider' => ['@type' => 'Organization', 'name' => 'Web4rtTech', 'url' => $url],
        ],
    ];
    if (!empty($service['faqs'])) {
        $jsonLd[] = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn ($faq) => [
                '@type' => 'Question',
                'name' => $faq['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['a']],
            ], $service['faqs']),
        ];
    }
    $quoteUrl = url('/contact').'?service='.$service['id'];
@endphp

@extends('layouts.site', [
    'title' => $service['title'],
    'description' => $service['description'],
    'path' => '/services/'.$service['id'],
    'jsonLd' => $jsonLd,
])

@section('content')
<main>
    <section class="page-hero service-detail-hero">
        <div class="container">
            <a href="{{ url('/services') }}#{{ $category['id'] }}" class="service-eyebrow">{{ $category['title'] }}</a>
            <h1>{{ $service['title'] }}</h1>
            <p>{{ $service['description'] }}</p>
        </div>
    </section>

    <section class="service-detail-content">
        <div class="container">
            <nav class="service-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ url('/') }}">Home</a>
                <span>/</span>
                <a href="{{ url('/services') }}">Services</a>
                <span>/</span>
                <span aria-current="page">{{ $service['title'] }}</span>
            </nav>

            <div class="service-detail-wrap">
                <div class="service-detail-icon">
                    @include('partials.service-icon', ['id' => $service['id']])
                </div>
                <h2>Overview</h2>
                <p>{{ $service['long'] }}</p>

                <h3>Key Offerings</h3>
                <ul class="offerings-list">
                    @foreach ($service['offerings'] as $offering)
                        <li>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 6 9 17 4 12" />
                            </svg>
                            {{ $offering }}
                        </li>
                    @endforeach
                </ul>

                @if (!empty($service['techStack']))
                    <h3>Tools &amp; Technologies</h3>
                    <div class="tech-stack">
                        @foreach ($service['techStack'] as $tech)
                            <span class="tech-pill">{{ $tech }}</span>
                        @endforeach
                    </div>
                @endif

                <a href="{{ $quoteUrl }}" class="service-cta">Get a Quote</a><a href="{{ url('/services') }}" class="back-link">← Back to Services</a>
            </div>
        </div>
    </section>

    @if (!empty($service['pricing']))
        <section class="service-pricing-section">
            <div class="container">
                <h2>Pricing Guide</h2>
                <p class="pricing-intro">Typical starting prices for {{ strtolower($service['title']) }} projects — pick a starting point, then we'll scope the details together.</p>
                <div class="pricing-grid">
                    @foreach ($service['pricing'] as $tier)
                        @php $featured = !empty($tier['featured']); $billing = $tier['billing'] ?? ''; @endphp
                        <div class="pricing-card {{ $featured ? 'featured' : '' }}">
                            @if ($featured)
                                <span class="pricing-badge">Most Popular</span>
                            @endif
                            <h3>{{ $tier['name'] }}</h3>
                            <p class="pricing-tagline">{{ $tier['tagline'] }}</p>
                            <div class="pricing-amount">
                                <span class="pricing-usd">{{ $tier['priceUSD'] }}{!! $billing ? '<span class="pricing-billing">'.e($billing).'</span>' : '' !!}</span>
                                @if ($tier['priceUSD'] !== $tier['priceINR'])
                                    <span class="pricing-inr">≈ {{ $tier['priceINR'] }}{{ $billing }}</span>
                                @endif
                            </div>
                            <ul class="pricing-features">
                                @foreach ($tier['features'] as $feature)
                                    <li>
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="20 6 9 17 4 12" />
                                        </svg>
                                        {{ $feature }}
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ $quoteUrl }}" class="pricing-cta {{ $featured ? 'primary' : '' }}">Get a Quote</a>
                        </div>
                    @endforeach
                </div>
                @if (!empty($service['pricingNote']))
                    <p class="pricing-note">{{ $service['pricingNote'] }}</p>
                @endif
            </div>
        </section>
    @endif

    <section class="service-process-section">
        <div class="container">
            <h2>Our {{ $category['title'] }} Process</h2>
            <div class="service-process-grid">
                @foreach ($category['process'] as $i => $step)
                    <div class="service-process-step">
                        <div class="service-process-number">{{ $i + 1 }}</div>
                        <h3>{{ $step['title'] }}</h3>
                        <p>{{ $step['description'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @if (!empty($service['faqs']))
        <section class="service-faq-section">
            <div class="container">
                <h2>Frequently Asked Questions</h2>
                <div class="faq-list">
                    @foreach ($service['faqs'] as $faq)
                        <div class="faq-item" data-faq>
                            <button class="faq-question" aria-expanded="false">
                                <span>{{ $faq['q'] }}</span>
                                <span class="faq-toggle">+</span>
                            </button>
                            <p class="faq-answer" hidden>{{ $faq['a'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if (count($related) > 0)
        <section class="related-services-section">
            <div class="container">
                <h2>Related Services</h2>
                <div class="related-services-grid">
                    @foreach ($related as $s)
                        <a href="{{ url('/services/'.$s['id']) }}" class="related-service-card">
                            <div class="related-service-icon">
                                @include('partials.service-icon', ['id' => $s['id']])
                            </div>
                            <div>
                                <h3>{{ $s['title'] }}</h3>
                                <p>{{ $s['description'] }}</p>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section class="service-cta-section">
        <div class="container">
            <h2>Ready to talk about your {{ strtolower($service['title']) }} project?</h2>
            <p>Tell us what you're trying to achieve and we'll get back to you within one business day.</p>
            <a href="{{ $quoteUrl }}">
                <button class="cta-button-primary">Get a Quote</button>
            </a>
        </div>
    </section>
</main>
@endsection
