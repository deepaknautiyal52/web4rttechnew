@php
    use App\Support\Site;
    $published = !empty($article->published_at) ? \App\Support\Meta::iso($article->published_at) : null;
    $jsonLd = array_filter([
        '@context' => 'https://schema.org',
        '@type' => 'NewsArticle',
        'headline' => $article->title,
        'description' => $article->excerpt ?? null,
        'url' => Site::URL.'/news/'.$slug,
        'datePublished' => $published,
        'publisher' => ['@type' => 'Organization', 'name' => 'Web4rtTech', 'url' => Site::URL],
    ], fn ($v) => $v !== null);
    $paragraphs = array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', (string) ($article->body ?? '')))));
@endphp

@extends('layouts.site', [
    'title' => $article->title,
    'description' => $article->excerpt ?? '',
    'path' => '/news/'.$slug,
    'type' => 'article',
    'jsonLd' => $jsonLd,
])

@section('content')
<main>
    <section class="page-hero">
        <div class="container">
            <h1>{{ $article->title }}</h1>
        </div>
    </section>

    <section class="news-article-content">
        <div class="container">
            @foreach ($paragraphs as $p)
                <p style="white-space: pre-line">{{ $p }}</p>
            @endforeach
            <a href="{{ url('/news') }}" class="back-link">← Back to News</a>
        </div>
    </section>
</main>
@endsection
