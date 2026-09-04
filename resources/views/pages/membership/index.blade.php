@php
    $title = 'Membership — LUX&GO';
    $description = 'LUX&GO Membership — five years of premium mobility access, built for flexibility and convenience.';
@endphp

@extends('layouts.app')

@section('body_class', 'has-light-top')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/membership.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/hero.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/membership-package.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/more-access.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/membership/understanding-usage.css') }}">
@endpush

@section('content')

    @include('pages.membership.sections.hero')
    @include('pages.membership.sections.membership-package')
    @include('pages.membership.sections.more-access')
    @include('pages.membership.sections.understanding-usage')

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/membership/calculator.js') }}" defer></script>
@endpush
