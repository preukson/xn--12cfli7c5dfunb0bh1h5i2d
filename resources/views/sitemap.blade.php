{!! '<?xml version="1.0" encoding="UTF-8"?>' !!}
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
  <url><loc>{{ route('home') }}</loc><changefreq>hourly</changefreq><priority>1.0</priority></url>
  <url><loc>{{ route('draws.index') }}</loc><changefreq>daily</changefreq><priority>0.8</priority></url>
@foreach ($draws as $draw)
  <url><loc>{{ $draw->url() }}</loc><lastmod>{{ $draw->updated_at->toAtomString() }}</lastmod><priority>0.6</priority></url>
@endforeach
</urlset>
