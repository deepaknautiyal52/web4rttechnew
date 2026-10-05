@php
    use App\Support\Site;
    $seoTitle = isset($title) && $title ? $title.' | '.Site::NAME : Site::NAME.' - Engineering Your Digital Future';
    $canonical = Site::URL.($path ?? '');
    $ogImage = $image ?? Site::URL.'/og-image.png';
    $jsonLdList = isset($jsonLd) ? (array_is_list($jsonLd) ? $jsonLd : [$jsonLd]) : [];
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="theme-color" content="#4F46E5" />
    <meta name="robots" content="index, follow" />
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}" />

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $description ?? '' }}" />
    <link rel="canonical" href="{{ $canonical }}" />

    <meta property="og:type" content="{{ $type ?? 'website' }}" />
    <meta property="og:site_name" content="{{ Site::NAME }}" />
    <meta property="og:title" content="{{ $seoTitle }}" />
    <meta property="og:description" content="{{ $description ?? '' }}" />
    <meta property="og:url" content="{{ $canonical }}" />
    <meta property="og:image" content="{{ $ogImage }}" />

    <meta name="twitter:card" content="summary_large_image" />
    <meta name="twitter:title" content="{{ $seoTitle }}" />
    <meta name="twitter:description" content="{{ $description ?? '' }}" />
    <meta name="twitter:image" content="{{ $ogImage }}" />

    <script type="application/ld+json">
      {
        "@context": "https://schema.org",
        "@type": "ProfessionalService",
        "name": "Web4rtTech",
        "url": "https://web4rttech.com",
        "logo": "https://web4rttech.com/favicon.svg",
        "image": "https://web4rttech.com/og-image.png",
        "description": "Digital-first IT services and consulting company offering web & mobile development, SEO, PPC, digital marketing, and AI & data science solutions.",
        "address": {
          "@type": "PostalAddress",
          "streetAddress": "Near Monal Farm",
          "addressLocality": "Dehradun",
          "addressRegion": "Uttarakhand",
          "postalCode": "248001",
          "addressCountry": "IN"
        },
        "telephone": "+91-7417120232",
        "email": "info@web4rttech.com",
        "priceRange": "$$"
      }
    </script>
    @foreach ($jsonLdList as $schema)
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
