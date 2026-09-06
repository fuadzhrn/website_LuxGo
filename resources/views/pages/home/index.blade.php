@php
    $title = __('home.meta.title');
    $description = __('home.meta.description');
@endphp

@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/home/home.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/home/hero.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/home/access-not-ownership.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/home/use-cases.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/home/premium-mobility.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/home/how-it-works.css') }}">
@endpush

@section('content')

    {{-- Order and visibility come from the CMS; the sections themselves are the
         same partials as before, each handed its own resolved content. --}}
    @foreach ($page->sections() as $key => $section)
        @foreach ($page->views($key) as $partial)
            @include($partial, ['s' => $section])
        @endforeach
    @endforeach

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/home/home.js') }}" defer></script>
@endpush
