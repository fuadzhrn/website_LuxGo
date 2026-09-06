@php
    $title = __('how-it-works.meta.title');
    $description = __('how-it-works.meta.description');
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
