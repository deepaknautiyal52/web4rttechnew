@extends('layouts.site', [
    'title' => 'Newsroom',
    'description' => 'Latest press releases, product announcements, and insights from Web4rtTech.',
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
