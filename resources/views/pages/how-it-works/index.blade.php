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

    {{-- Order and visibility come from the CMS; the partials are the approved
         ones, each handed its own resolved content. --}}
    @foreach ($page->sections() as $key => $section)
        @foreach ($page->views($key) as $partial)
            @include($partial, ['s' => $section, 'page' => $page])
        @endforeach
    @endforeach

@endsection
