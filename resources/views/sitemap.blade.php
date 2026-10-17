{!! '<' . '?xml version="1.0" encoding="UTF-8"?' . '>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    @foreach ($staticUrls as $url)
        <url>
            <loc>{{ $url }}</loc>
            <lastmod>{{ date('Y-m-d') }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.8</priority>
        </url>
    @endforeach

    @foreach ($courses as $course)
        <url>
            <loc>{{ route('courses.show', $course->slug) }}</loc>
            <lastmod>{{ $course->updated_at ? $course->updated_at->format('Y-m-d') : date('Y-m-d') }}</lastmod>
            <changefreq>weekly</changefreq>
            <priority>0.9</priority>
        </url>
    @endforeach
</urlset>
