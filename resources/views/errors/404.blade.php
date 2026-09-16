@php
    $title = __('global.not_found.title').' — '.config('app.name');
    $description = __('global.not_found.copy');

    /* A missing page has nothing to rank for, and every one of them would be a
       near-duplicate of the others. Links are still followed so the crawler can
       find its way back into the site. */
    $robots = 'noindex, follow';
@endphp

@extends('layouts.app')

@section('body_class', 'has-light-top')

@push('styles')
    {{-- The legal shell already is a centred message on a light page, which is
         exactly what this needs; no stylesheet of its own. --}}
    <link rel="stylesheet" href="{{ asset('assets/css/pages/legal/legal.css') }}">
@endpush

@section('content')

    <section class="legal-page">
        <div class="lux-container legal-page__inner">
            <p class="legal-page__eyebrow" data-enter>{{ __('global.not_found.eyebrow') }}</p>

            <h1 class="legal-page__title" data-enter data-enter-delay="1">{{ __('global.not_found.title') }}</h1>

            <p class="legal-page__copy" data-enter data-enter-delay="2">{{ __('global.not_found.copy') }}</p>

            <p class="legal-page__contact" data-enter data-enter-delay="3">
                <a href="{{ route('home', ['locale' => app()->getLocale()]) }}" class="lux-btn lux-btn-primary">
                    {{ __('global.not_found.back') }}
                </a>
            </p>
        </div>
    </section>

@endsection
