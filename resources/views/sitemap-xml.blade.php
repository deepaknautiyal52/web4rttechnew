{{-- /sitemap.xml for search engines, built from the service catalogue and published articles. --}}
@php
    use App\Support\Site;
    $urls = [
        ['/', 'weekly', '1.0'],
        ['/services', 'weekly', '0.9'],
        ['/contact', 'monthly', '0.9'],
        ['/about', 'monthly', '0.7'],
    ];
    foreach (Site::services() as $s) {
        $urls[] = ['/services/'.$s['id'], 'monthly', '0.8'];
    }
    $urls[] = ['/news', 'weekly', '0.6'];
    foreach (Site::articles() as $a) {
        $urls[] = ['/news/'.$a->slug, 'yearly', '0.5', $a->published_at ?? null];
    }
    array_push($urls,
        ['/careers', 'weekly', '0.6'],
        ['/ventures', 'monthly', '0.6'],
        ['/sitemap', 'monthly', '0.3'],
        ['/terms', 'yearly', '0.3'],
        ['/privacy', 'yearly', '0.3'],
        ['/cookies', 'yearly', '0.3'],
    );
    // Built in pieces so editors don't mistake "<?" for a PHP open tag.
    $xmlDeclaration = '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';
@endphp
{!! $xmlDeclaration !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
@foreach ($urls as $u)
  <url>
    <loc>{{ Site::URL.$u[0] }}</loc>
@if (!empty($u[3]))
    <lastmod>{{ \Illuminate\Support\Carbon::parse($u[3])->toDateString() }}</lastmod>
@endif
    <changefreq>{{ $u[1] }}</changefreq>
    <priority>{{ $u[2] }}</priority>
  </url>
@endforeach
</urlset>
