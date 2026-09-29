@extends('layouts.site', [
    'title' => 'Site Map',
    'description' => 'Browse every page on the Web4rtTech website, organized by section.',
    'path' => '/sitemap',
])

@php
    use App\Support\Site;
    $newsArticles = [
        ['id' => 'ai-first-delivery', 'title' => 'Web4rtTech Launches AI-First Delivery Framework'],
        ['id' => 'cloud-fabric', 'title' => 'Web4rtTech Cloud Fabric™ Now Live'],
        ['id' => 'innovations-2025', 'title' => 'New Innovations in Tech'],
    ];
@endphp

@section('content')
<main>
    <section class="page-hero">
        <div class="container page-hero-flex">
            <div class="page-hero-text">
                <h1>Site Map</h1>
                <p>Every page on Web4rtTech, in one place</p>
            </div>
            <div class="page-hero-illustration">
                @include('partials.hero-illustration', ['variant' => 'careers'])
            </div>
        </div>
    </section>

    <section class="sitemap-content">
        <div class="container">
            <div class="sitemap-grid">
                <div class="sitemap-section">
                    <h2>Company</h2>
                    <ul>
                        <li><a href="{{ url('/') }}">Home</a></li>
                        <li><a href="{{ url('/about') }}">About Us</a></li>
                        <li><a href="{{ url('/careers') }}">Careers</a></li>
                        <li><a href="{{ url('/ventures') }}">Our Ventures</a></li>
                        <li><a href="{{ url('/news') }}">Newsroom</a></li>
                        <li><a href="{{ url('/contact') }}">Contact / Get a Quote</a></li>
                    </ul>
                </div>

                @foreach (Site::categories() as $category)
                    <div class="sitemap-section">
                        <h2>{{ $category['title'] }}</h2>
                        <ul>
                            <li><a href="{{ url('/services') }}#{{ $category['id'] }}">All {{ $category['title'] }}</a></li>
                            @foreach (Site::servicesIn($category['id']) as $s)
                                <li><a href="{{ url('/services/'.$s['id']) }}">{{ $s['title'] }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach

                <div class="sitemap-section">
                    <h2>Our Ventures</h2>
                    <ul>
                        <li><a href="{{ url('/ventures') }}">All Ventures</a></li>
                        @foreach (Site::ventures() as $v)
                            <li>
                                @if ($v['url'])
                                    <a href="{{ $v['url'] }}" target="_blank" rel="noopener noreferrer">{{ $v['name'] }} ↗</a>
                                @else
                                    <a href="{{ url('/ventures') }}#{{ $v['id'] }}">{{ $v['name'] }}</a>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="sitemap-section">
                    <h2>Newsroom</h2>
                    <ul>
                        <li><a href="{{ url('/news') }}">All News</a></li>
                        @foreach ($newsArticles as $article)
                            <li><a href="{{ url('/news/'.$article['id']) }}">{{ $article['title'] }}</a></li>
                        @endforeach
                    </ul>
                </div>

                <div class="sitemap-section">
                    <h2>Legal</h2>
                    <ul>
                        <li><a href="{{ url('/terms') }}">Terms of Use</a></li>
                        <li><a href="{{ url('/privacy') }}">Privacy Policy</a></li>
                        <li><a href="{{ url('/cookies') }}">Cookie Policy</a></li>
                        <li><a href="{{ url('/sitemap') }}">Site Map</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</main>
@endsection
