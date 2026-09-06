@php
    $title = $legalTitle.' — LUX&GO';
    $description = $legalTitle.' for LUX&GO Premium Mobility Membership.';
@endphp

@extends('layouts.app')

@section('body_class', 'has-light-top')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/pages/legal/legal.css') }}">
@endpush

@section('content')

    <section class="legal-page">
        <div class="lux-container legal-page__inner">
            <p class="legal-page__eyebrow" data-enter>{{ __('global.legal.eyebrow') }}</p>

            <h1 class="legal-page__title" data-enter data-enter-delay="1">{{ $legalTitle }}</h1>

            <p class="legal-page__copy" data-enter data-enter-delay="2">
                {{ __('global.legal.preparing', ['title' => $legalTitle]) }}
            </p>

            <p class="legal-page__contact" data-enter data-enter-delay="3">
                {{ __('global.legal.contact') }}
                <a href="mailto:info@luxandgo.com" class="legal-page__link">info@luxandgo.com</a>.
            </p>
        </div>
    </section>

@endsection
