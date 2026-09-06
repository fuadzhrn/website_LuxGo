@php
    $title = __('collection.meta.title');
    $description = __('collection.meta.description');
@endphp

@extends('layouts.app')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/collection/collection.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/collection/hero.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/collection/featured-vehicle.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/collection/inside-experience.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pages/collection/collection-cta.css') }}">
@endpush

@section('content')

    @include('pages.collection.sections.hero')
    @include('pages.collection.sections.featured-vehicle')
    @include('pages.collection.sections.inside-experience')
    @include('pages.collection.sections.collection-cta')

@endsection
