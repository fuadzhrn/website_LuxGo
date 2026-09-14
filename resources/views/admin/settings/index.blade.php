@extends('admin.layouts.app')

@section('title', 'Settings')

@php
    $groups = [
        'company' => 'Company',
        'contact' => 'Contact',
        'social' => 'Social',
    ];
@endphp

@section('content')

    <x-admin.ui.section-header
        title="Settings"
        description="The company details the whole site shows. They are stored once here and used by the footer on every page, the contact section and the legal pages."
    />

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        @foreach ($groups as $group => $label)
            <div class="admin-panel">
                <x-admin.ui.section-header :title="$label" />

                @foreach ($fields as $key => $field)
                    @continue($field['group'] !== $group)

                    @if ($field['textarea'] ?? false)
                        <x-admin.form.textarea
                            :name="$key"
                            :label="$field['label']"
                            :value="$values[$key] ?? null"
                            :help="$field['help'] ?? null"
                            :rows="4"
                            required
                        />
                    @else
                        <x-admin.form.input
                            :name="$key"
                            :label="$field['label']"
                            :value="$values[$key] ?? null"
                            :help="$field['help'] ?? null"
                            :required="in_array('required', $field['rules'], true)"
                        />
                    @endif
                @endforeach
            </div>
        @endforeach

        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Links built from these details"
                description="Worked out when a page renders, never stored — change a detail above and every link follows."
            />

            <dl class="admin-detail">
                <div class="admin-detail__row">
                    <dt class="admin-detail__label">Phone link</dt>
                    <dd class="admin-detail__value">{{ $settings->phoneLink() ?? '—' }}</dd>
                </div>
                <div class="admin-detail__row">
                    <dt class="admin-detail__label">Email link</dt>
                    <dd class="admin-detail__value">{{ $settings->emailLink() ?? '—' }}</dd>
                </div>
                <div class="admin-detail__row">
                    <dt class="admin-detail__label">Instagram</dt>
                    <dd class="admin-detail__value">{{ $settings->instagramUrl() ?? 'Not shown' }}</dd>
                </div>
                <div class="admin-detail__row">
                    <dt class="admin-detail__label">TikTok</dt>
                    <dd class="admin-detail__value">{{ $settings->tiktokUrl() ?? 'Not shown' }}</dd>
                </div>
            </dl>
        </div>

        <x-admin.ui.save-bar :updated-at="$updatedAt ? \Illuminate\Support\Carbon::parse($updatedAt) : null" />
    </form>

@endsection
