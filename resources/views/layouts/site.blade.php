@php
    use App\Support\Site;
    $seoTitle = isset($title) && $title ? $title.' | '.Site::NAME : Site::NAME.' - Engineering Your Digital Future';
    $canonical = Site::URL.($path ?? '');
    $ogImage = $image ?? Site::URL.'/og-image.png';
    $jsonLdList = isset($jsonLd) ? (array_is_list($jsonLd) ? $jsonLd : [$jsonLd]) : [];
    // The business itself, on every page. Pages refer to it by its @id.
    $organization = [
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        '@id' => Site::URL.'/#organization',
        'name' => Site::NAME,
        'url' => Site::URL,
        'logo' => Site::URL.'/og-image.png',
        'image' => Site::URL.'/og-image.png',
        'description' => 'IT services company in Dehradun, Uttarakhand offering website and mobile app development, domain registration, web hosting, business email, SEO, PPC, digital marketing, and AI & data science solutions.',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => 'Near Monal Farm',
            'addressLocality' => 'Dehradun',
            'addressRegion' => 'Uttarakhand',
            'postalCode' => '248001',
            'addressCountry' => 'IN',
        ],
        'areaServed' => [['@type' => 'Country', 'name' => 'India'], 'Worldwide'],
        'telephone' => '+91-7417120232',
        'email' => 'info@web4rttech.com',
        'priceRange' => '₹₹',
        'knowsAbout' => ['Website development', 'Mobile app development', 'Domain registration', 'Web hosting', 'Business email', 'SEO', 'Google Ads', 'Digital marketing', 'AI solutions', 'Data analytics'],
    ];
    $website = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        '@id' => Site::URL.'/#website',
        'name' => Site::NAME,
        'url' => Site::URL,
        'inLanguage' => 'en-IN',
        'publisher' => ['@id' => Site::URL.'/#organization'],
    ];
@endphp
<!DOCTYPE html>
<html lang="en-IN">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#4F46E5" />
    <meta name="robots" content="{{ !empty($noindex) ? 'noindex, follow' : 'index, follow, max-image-preview:large' }}" />
    <meta name="geo.region" content="IN-UT" />
    <meta name="geo.placename" content="Dehradun" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32" />
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}" />

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $description ?? '' }}" />
    <link rel="canonical" href="{{ $canonical }}" />

    <meta property="og:type" content="{{ $type ?? 'website' }}" />
    <meta property="og:locale" content="en_IN" />
    <meta property="og:site_name" content="{{ Site::NAME }}" />
    <meta property="og:title" content="{{ $seoTitle }}" />
    <meta property="og:description" content="{{ $description ?? '' }}" />
    <meta property="og:url" content="{{ $canonical }}" />
    <meta property="og:image" content="{{ $ogImage }}" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $seoTitle }}" />
    <meta name="twitter:description" content="{{ $description ?? '' }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />

    @foreach (array_merge([$organization, $website], $jsonLdList) as $schema)
        <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
    @endforeach

    <link rel="stylesheet" href="{{ asset('css/app.css') }}?v={{ filemtime(public_path('css/app.css')) }}" />
</head>
<body>
    <div id="root">
        <div class="App">
            @include('partials.header')
            @yield('content')
            @include('partials.footer')
        </div>
    </div>
    <script src="{{ asset('js/site.js') }}?v={{ filemtime(public_path('js/site.js')) }}" defer></script>
</body>
</html>
