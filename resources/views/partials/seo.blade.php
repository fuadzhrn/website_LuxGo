<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>{{ $title ?? config('app.name') }}</title>
<meta name="description" content="{{ $description ?? 'LUX&GO — Premium Mobility Membership.' }}">

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
