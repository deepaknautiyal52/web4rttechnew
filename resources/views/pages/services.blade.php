@extends('layouts.site', [
    'title' => 'Our Services',
    'description' => "Explore Web4rtTech's services: web & mobile development, cloud services, SEO, SMO, PPC & Google Ads, digital marketing strategy, AdSense monetization, AI solutions, and data science.",
    'path' => '/services',
])

@section('content')
<main>
    <section class="page-hero">
        <div class="container page-hero-flex">
            <div class="page-hero-text">
                <h1>Our Services</h1>
                <p>Development, growth marketing, and AI/data solutions for businesses in India and worldwide</p>
            </div>
            <div class="page-hero-illustration">
                @include('partials.hero-illustration', ['variant' => 'services'])
            </div>
        </div>
    </section>

    <section class="services-content">
        <div class="container">
            <div class="services-intro">
                <h2>What We Offer</h2>
                <p>
                    Web4rtTech provides end-to-end technology and growth services that help businesses build
                    great products and get them in front of the right customers.
                </p>
            </div>

            @foreach (\App\Support\Site::categories() as $category)
                <div id="{{ $category['id'] }}" class="services-category">
                    <div class="services-category-header">
                        <h3>{{ $category['title'] }}</h3>
                        <p>{{ $category['description'] }}</p>
                    </div>

                    <div class="services-large-grid">
                        @foreach (\App\Support\Site::servicesIn($category['id']) as $service)
                            <div class="service-detail-card">
                                <div class="service-icon-large">
                                    @include('partials.service-icon', ['id' => $service['id']])
                                </div>
                                <h3>{{ $service['title'] }}</h3>
                                <p>{{ $service['description'] }}</p>
                                <a href="{{ url('/services/'.$service['id']) }}" class="service-link">Learn More →</a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    <section class="service-benefits">
        <div class="container">
            <h2>Why Choose Web4rtTech?</h2>
            <div class="benefits-grid">
                <div class="benefit-item">
                    <h3>One Team, Every Channel</h3>
                    <p>Development and growth marketing under one roof — your site, app, SEO, and ads all work toward the same goal</p>
                </div>
                <div class="benefit-item">
                    <h3>Built for Global Clients</h3>
                    <p>Comfortable working across time zones and currencies, with clear async communication and transparent reporting</p>
                </div>
                <div class="benefit-item">
                    <h3>Data-Backed Decisions</h3>
                    <p>Every campaign and product decision is measured — you always know what's working and why</p>
                </div>
                <div class="benefit-item">
                    <h3>Direct Access, No Layers</h3>
                    <p>You work directly with the people building your product and running your campaigns, not account managers relaying messages</p>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
