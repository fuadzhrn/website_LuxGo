<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>

    @include('partials.seo')

    <link rel="stylesheet" href="{{ asset('assets/css/global.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components/animations.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components/buttons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components/header.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/components/footer.css') }}">

    @stack('styles')

</head>
<body class="@yield('body_class')">

    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    <script src="{{ asset('assets/js/global.js') }}" defer></script>
    <script src="{{ asset('assets/js/components/header.js') }}" defer></script>
    <script src="{{ asset('assets/js/components/reveal.js') }}" defer></script>

    @stack('scripts')

</body>
</html>
