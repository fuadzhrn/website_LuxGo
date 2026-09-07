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

    {{-- Order and visibility come from the CMS; the partials are the approved
         ones, each handed its own resolved content. --}}
    @foreach ($page->sections() as $key => $section)
        @foreach ($page->views($key) as $partial)
            @include($partial, ['s' => $section, 'page' => $page])
        @endforeach
    @endforeach

@endsection
