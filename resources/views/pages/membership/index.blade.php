@php
    $title = __('membership.meta.title');
    $description = __('membership.meta.description');
@endphp

@extends('layouts.app')

@section('body_class', 'has-light-top')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/membership.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/hero.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/membership-package.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/more-access.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/understanding-usage.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/faq.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/membership-cta.css') }}">
@endpush

@section('content')

    {{-- Order and visibility come from the CMS; the figures come from the
         membership settings, which every partial receives. --}}
    @foreach ($page->sections() as $key => $section)
        @foreach ($page->views($key) as $partial)
            @include($partial, ['s' => $section, 'membership' => $membership])
        @endforeach
    @endforeach

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/membership/calculator.js') }}" defer></script>
    <script src="{{ asset('assets/js/pages/membership/faq.js') }}" defer></script>
@endpush
