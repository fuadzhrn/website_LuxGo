@extends('admin.layouts.app')

@section('title', $definition['label'].' page')

@section('content')

    <nav class="admin-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.content') }}">Content</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{{ $definition['label'] }}</span>
    </nav>

    <x-admin.ui.section-header
        :title="$definition['label'].' page'"
        description="Each section is edited on its own, in both languages. Turning a section off keeps its content and hides it from the public page."
    />

    @if ($definition['business_settings'] ?? false)
        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Business settings"
                description="Prices, usage rights and durations. Stored once and used by every section, the calculator and the FAQ."
            />

            <a class="admin-button admin-button--ghost" href="{{ route('admin.content.business-settings', $page) }}">Edit business settings</a>
        </div>
    @endif

    @if ($definition['vehicles'] ?? false)
        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Vehicles"
                description="The vehicles this page shows, with their own copy, imagery and order."
            />

            <a class="admin-button admin-button--ghost" href="{{ route('admin.vehicles') }}">Manage vehicles</a>
        </div>
    @endif

    <div class="admin-panel">
        <ul class="admin-sections">
            @foreach ($sections as $item)
                <li class="admin-sections__item">
                    <div class="admin-sections__body">
                        <p class="admin-sections__name">{{ $item['label'] }}</p>

                        <span class="admin-pill{{ $item['model']->is_active ? ' admin-pill--on' : '' }}">
                            {{ $item['model']->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <a
                        class="admin-button admin-button--ghost"
                        href="{{ route('admin.content.section.edit', [$page, $item['model']]) }}"
                    >Edit</a>
                </li>
            @endforeach
        </ul>
    </div>

@endsection
