@extends('admin.layouts.app')

@php
    $isNew = ! $item->exists;
    $locales = config('locales.supported');
@endphp

@section('title', $isNew ? 'Add FAQ' : 'Edit FAQ')

@section('content')

    <nav class="admin-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.content') }}">Content</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('admin.content.page', $page) }}">{{ config('page_content.pages.'.$page->key.'.label') }}</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('admin.content.faq', [$page, $section]) }}">FAQ</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{{ $isNew ? 'Add' : 'Edit' }}</span>
    </nav>

    <x-admin.ui.section-header
        :title="$isNew ? 'Add FAQ' : 'Edit FAQ'"
        description="The question and answer are entered per language. Figures are written as placeholders so they follow the business settings."
    />

    <form
        method="POST"
        action="{{ $isNew ? route('admin.content.faq.store', [$page, $section]) : route('admin.content.faq.update', [$page, $section, $item]) }}"
    >
        @csrf
        @unless ($isNew)
            @method('PUT')
        @endunless

        <div class="admin-panel">
            <x-admin.content.language-tabs>
                @foreach ($locales as $index => $locale)
                    <x-admin.content.language-panel :locale="$locale" :active="$index === 0">
                        <x-admin.form.input
                            :name="'question['.$locale.']'"
                            label="Question"
                            :value="$item->translation($locale)?->question"
                        />

                        <x-admin.form.textarea
                            :name="'answer['.$locale.']'"
                            label="Answer"
                            :value="$item->translation($locale)?->answer"
                            :rows="4"
                            :help="'Placeholders: '.implode(' ', App\Support\MembershipValues::allowedPlaceholders())"
                        />
                    </x-admin.content.language-panel>
                @endforeach
            </x-admin.content.language-tabs>
        </div>

        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Shared"
                description="The same in both languages."
            />

            {{-- The figures in the breakdown come from the business settings,
                 so switching it on never means typing an amount. --}}
            <div class="admin-field">
                <p class="admin-label" id="f-breakdown-label">Usage cost breakdown</p>

                <input type="hidden" name="shows_usage_breakdown" value="0">

                <label class="admin-toggle" for="f-breakdown">
                    <input
                        class="admin-toggle__input"
                        type="checkbox"
                        id="f-breakdown"
                        name="shows_usage_breakdown"
                        value="1"
                        @checked(old('shows_usage_breakdown', $item->shows_usage_breakdown))
                    >
                    <span class="admin-toggle__track" aria-hidden="true"><span class="admin-toggle__thumb"></span></span>
                    <span class="admin-toggle__text">Show under the answer</span>
                </label>

                <p class="admin-help">Adds the regular + additional usage cost table beneath this answer.</p>
            </div>

            <x-admin.content.status-toggle
                :checked="$item->is_active"
                help="An inactive question keeps its content but is not shown on the public page."
            />
        </div>

        <x-admin.ui.save-bar :updated-at="$item->exists ? $item->updated_at : null">
            <a class="admin-button admin-button--quiet" href="{{ route('admin.content.faq', [$page, $section]) }}">Cancel</a>
        </x-admin.ui.save-bar>
    </form>

    @unless ($isNew)
        <div class="admin-panel">
            {{-- A disclosure rather than a JS confirm, so the second deliberate
                 click is required with or without JS. --}}
            <details class="admin-confirm">
                <summary class="admin-confirm__summary">Delete FAQ</summary>

                <div class="admin-confirm__body">
                    <p class="admin-confirm__copy">Delete this question? This action cannot be undone.</p>

                    <form method="POST" action="{{ route('admin.content.faq.destroy', [$page, $section, $item]) }}">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="admin-button admin-button--danger">Delete</button>
                        <a class="admin-button admin-button--quiet" href="{{ route('admin.content.faq', [$page, $section]) }}">Cancel</a>
                    </form>
                </div>
            </details>
        </div>
    @endunless

@endsection
