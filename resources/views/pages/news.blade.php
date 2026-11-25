@extends('layouts.site', [
    'title' => 'News & Insights: Web, SEO & AI Updates',
    'description' => 'News, announcements and practical insights on web development, SEO, digital marketing and AI from the Web4rtTech team in Dehradun.',
    'path' => '/news',
])

@section('content')
<main>
    <section class="page-hero">
        <div class="container">
            <h1>Newsroom</h1>
            <p>Latest press releases, announcements and insights</p>
        </div>
    </section>

    <section class="news-list-section">
        <div class="container">
            <div class="news-grid">
                @forelse ($articles as $a)
                    <article class="news-card">
                        <h3>{{ $a->title }}</h3>
                        <p>{{ $a->excerpt }}</p>
                        <a href="{{ url('/news/'.$a->slug) }}" class="read-more">Read more →</a>
                    </article>
                @empty
                    <p>No news yet. Check back soon.</p>
                @endforelse
            </div>
        </div>
    </section>
</main>
@endsection
