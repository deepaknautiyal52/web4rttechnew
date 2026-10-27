@php
    use App\Support\Site;
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'Web4rtTech',
        'url' => Site::URL,
        'subOrganization' => array_map(fn ($v) => array_filter([
            '@type' => 'Organization',
            'name' => $v['name'],
            'description' => $v['description'],
            'url' => $v['url'] ?: null,
        ]), Site::ventures()),
    ];
@endphp

@extends('layouts.site', [
    'title' => 'Our Ventures',
    'description' => 'Beyond IT services, the Web4rtTech family runs homestays in Uttarakhand, a real estate business, yoga & wellness programmes, and CCTV security and surveillance services.',
    'path' => '/ventures',
    'jsonLd' => $jsonLd,
])

@section('content')
<main>
    <section class="page-hero">
        <div class="container">
            <h1>Our Ventures</h1>
            <p>Beyond technology: hospitality, property, wellness and security, from the same team</p>
        </div>
    </section>

    <section class="ventures-intro">
        <div class="container">
            <p>
                Web4rtTech is our technology company, but it is not all we do. From our roots in Uttarakhand we also run
                four other businesses. Whether you are planning a stay in the hills, looking for property, starting your yoga
                journey, or securing your premises with CCTV, you are dealing with the same people you can trust with your
                software.
            </p>
        </div>
    </section>

    <section class="ventures-list">
        <div class="container">
            @include('partials.venture-grid', ['detailed' => true])
        </div>
    </section>

    <section class="ventures-cta">
        <div class="container">
            <h2>Not sure where to start?</h2>
            <p>Tell us what you need, whether it is a website, a stay, a property, a yoga programme or a CCTV installation, and we will point you to the right team.</p>
            <a href="{{ url('/contact') }}" class="ventures-cta-btn">Contact Us →</a>
        </div>
    </section>
</main>
@endsection
