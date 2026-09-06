@php
    $title = __('about.meta.title');
    $description = __('about.meta.description');
@endphp

@extends('layouts.app')

{{-- The page opens on a light surface, so the navbar needs its solid state. --}}
@section('body_class', 'has-light-top')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/about-contact/about-contact.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/about-contact/about.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/about-contact/membership-application.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/about-contact/contact-head-office.css') }}">
@endpush

@section('content')

    @include('pages.about-contact.sections.about')
    @include('pages.about-contact.sections.membership-application')
    @include('pages.about-contact.sections.contact-head-office')

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/pages/about-contact/membership-application.js') }}" defer></script>
@endpush
