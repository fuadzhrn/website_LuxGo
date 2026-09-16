@php
    /* $seo is resolved from the database for the six CMS pages; a page without
       an SEO record (the legal pages) falls back to what the view passed in.
       Everything below is worked out here so the tags stay a flat list, and so
       a page without a record still gets a canonical, a robots rule and a
       sharing card rather than none at all. */
    $seoTitle = $seo?->title() ?? $title ?? config('app.name');
    $seoDescription = $seo?->description() ?? $description ?? '';
    $seoCanonical = $seo?->canonical() ?? url()->current();
    $seoRobots = $seo?->robots() ?? ($robots ?? 'index, follow');
    $seoOgTitle = $seo?->ogTitle() ?? $seoTitle;
    $seoOgDescription = $seo?->ogDescription() ?? $seoDescription;

    /* The sharing card falls back to a 1200x630 crop of the home hero, which is
       the best image the site already owns. Its size is known, so it can be
       declared; an image chosen in the CMS is left for the scraper to measure. */
    $seoOgImage = $seo?->ogImageUrl();
    $seoOgImageIsFallback = $seoOgImage === null;
    $seoOgImage ??= asset('assets/images/luxgo/global/og-default.jpg');
@endphp

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">
<meta name="robots" content="{{ $seoRobots }}">
<link rel="canonical" href="{{ $seoCanonical }}">

{{-- Sharing: the share wording falls back to the search wording, so it is
     written once unless it is deliberately different. --}}
<meta property="og:type" content="website">
<meta property="og:site_name" content="{{ config('app.name') }}">
<meta property="og:locale" content="{{ app()->getLocale() }}">
@foreach (array_diff(config('locales.supported'), [app()->getLocale()]) as $alternateLocale)
    <meta property="og:locale:alternate" content="{{ $alternateLocale }}">
@endforeach
<meta property="og:url" content="{{ $seoCanonical }}">
<meta property="og:title" content="{{ $seoOgTitle }}">
<meta property="og:description" content="{{ $seoOgDescription }}">
<meta property="og:image" content="{{ $seoOgImage }}">
@if ($seoOgImageIsFallback)
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $seoOgTitle }}">
<meta name="twitter:description" content="{{ $seoOgDescription }}">
<meta name="twitter:image" content="{{ $seoOgImage }}">

{{-- Alternate language versions of this exact page. --}}
@isset($localeAlternates)
    @foreach ($localeAlternates as $code => $alternate)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $alternate['url'] }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $localeAlternates[config('locales.default')]['url'] }}">
@endisset

@if ($googleSiteVerification = config('services.google.site_verification'))
    <meta name="google-site-verification" content="{{ $googleSiteVerification }}">
@endif

@include('partials.favicon')

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@500;600&family=Outfit:wght@300;700&display=swap" rel="stylesheet">
{{-- The logo's ampersand is not Outfit's — it has the straight leg and flat cut
     of a grotesque. Poppins draws it almost exactly, so it is requested for that
     one character (724 bytes) and carries a unicode-range of U+26, which keeps
     it from reaching any other text. --}}
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&text=%26&display=swap" rel="stylesheet">
