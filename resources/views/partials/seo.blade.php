@php
    /* $seo is resolved from the database for the six CMS pages; a page without
       an SEO record (the legal pages) falls back to what the view passed in. */
    $seoTitle = $seo?->title() ?? $title ?? config('app.name');
    $seoDescription = $seo?->description() ?? $description ?? '';
@endphp

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $seoTitle }}</title>
<meta name="description" content="{{ $seoDescription }}">

@isset($seo)
    <meta name="robots" content="{{ $seo->robots() }}">
    <link rel="canonical" href="{{ $seo->canonical() }}">

    {{-- Sharing: the share wording falls back to the search wording, so it is
         written once unless it is deliberately different. --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="{{ app()->getLocale() }}">
    <meta property="og:url" content="{{ $seo->canonical() }}">
    <meta property="og:title" content="{{ $seo->ogTitle() }}">
    <meta property="og:description" content="{{ $seo->ogDescription() }}">

    @if ($seo->ogImageUrl())
        <meta property="og:image" content="{{ $seo->ogImageUrl() }}">
        <meta name="twitter:card" content="summary_large_image">
    @endif
@endisset

{{-- Alternate language versions of this exact page. --}}
@isset($localeAlternates)
    @foreach ($localeAlternates as $code => $alternate)
        <link rel="alternate" hreflang="{{ $code }}" href="{{ $alternate['url'] }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $localeAlternates[config('locales.default')]['url'] }}">
@endisset

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@500;600&display=swap" rel="stylesheet">
