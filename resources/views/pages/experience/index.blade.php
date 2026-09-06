@php
    $title = __('experience.meta.title');
    $description = __('experience.meta.description');
@endphp

@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/experience/experience.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/experience/hero.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/experience/not-just-driver.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/experience/service-standard.css') }}">
@endpush

@section('content')

    @include('pages.experience.sections.hero')
    @include('pages.experience.sections.not-just-driver')
    @include('pages.experience.sections.service-standard')

@endsection
