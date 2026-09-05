@php
    $title = 'How It Works — LUX&GO';
    $description = 'From membership to your journey — join, book, and use premium mobility in three simple steps.';
@endphp

@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/how-it-works/how-it-works.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/how-it-works/hero.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/how-it-works/process.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/how-it-works/service-area.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/how-it-works/closing-cta.css') }}">
@endpush

@section('content')

    @include('pages.how-it-works.sections.hero')
    @include('pages.how-it-works.sections.process')
    @include('pages.how-it-works.sections.service-area')
    @include('pages.how-it-works.sections.closing-cta')

@endsection
