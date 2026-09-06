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

    @include('pages.home.sections.hero')
    @include('pages.home.sections.access-not-ownership')
    @include('pages.home.sections.use-cases')
    @include('pages.home.sections.premium-mobility')
    @include('pages.home.sections.how-it-works')

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/home/home.js') }}" defer></script>
@endpush
