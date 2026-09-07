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

    {{-- Order and visibility come from the CMS; the vehicles are handed to the
         sections that show them. --}}
    @foreach ($page->sections() as $key => $section)
        @foreach ($page->views($key) as $partial)
            @include($partial, ['s' => $section, 'vehicles' => $vehicles])
        @endforeach
    @endforeach

@endsection
