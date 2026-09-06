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

    @include('pages.membership.sections.hero')
    @include('pages.membership.sections.membership-package')
    @include('pages.membership.sections.more-access')
    @include('pages.membership.sections.understanding-usage')
    @include('pages.membership.sections.faq')
    @include('pages.membership.sections.membership-cta')

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/membership/calculator.js') }}" defer></script>
    <script src="{{ asset('assets/js/pages/membership/faq.js') }}" defer></script>
@endpush
