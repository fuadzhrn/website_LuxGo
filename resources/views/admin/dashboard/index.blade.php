@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

    <p class="admin-welcome">Welcome back, {{ auth()->user()->name }}.</p>

    {{-- Every figure below is a live count, never a placeholder. --}}
    <div class="admin-stats">
        <div class="admin-stat">
            <p class="admin-stat__value">{{ $pageCount }}</p>
            <p class="admin-stat__label">Pages</p>
        </div>

        <div class="admin-stat">
            <p class="admin-stat__value">{{ $mediaCount }}</p>
            <p class="admin-stat__label">Media files</p>
        </div>

        <div class="admin-stat">
            <p class="admin-stat__value">{{ $applicationCount }}</p>
            <p class="admin-stat__label">Applications</p>
        </div>

        <div class="admin-stat">
            <p class="admin-stat__value">{{ $newApplicationCount }}</p>
            <p class="admin-stat__label">New applications</p>
        </div>
    </div>

    <h2 class="admin-heading">Quick access</h2>

    <div class="admin-quick">
        <a class="admin-quick__link" href="{{ route('admin.content') }}">Manage Content</a>
        <a class="admin-quick__link" href="{{ route('admin.media') }}">Media</a>
        <a class="admin-quick__link" href="{{ route('admin.applications') }}">Membership Applications</a>
        <a class="admin-quick__link" href="{{ route('admin.seo') }}">SEO</a>
        <a class="admin-quick__link" href="{{ route('admin.settings') }}">Settings</a>
    </div>

@endsection
