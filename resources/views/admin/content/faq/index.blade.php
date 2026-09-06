@extends('admin.layouts.app')

@section('title', 'FAQ')

@php($editLocale = config('locales.default'))

@section('content')

    <nav class="admin-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.content') }}">Content</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('admin.content.page', $page) }}">{{ config('page_content.pages.'.$page->key.'.label') }}</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('admin.content.section.edit', [$page, $section]) }}">{{ $definition['label'] }}</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">FAQ</span>
    </nav>

    <x-admin.ui.section-header
        title="FAQ"
        description="Questions are shown in this order on the public page. An inactive question keeps its content but is not shown."
    />

    <div class="admin-panel">
        <a class="admin-button admin-button--primary" href="{{ route('admin.content.faq.create', [$page, $section]) }}">Add FAQ</a>
    </div>

    <div class="admin-panel">
        @if ($items->isEmpty())
            <x-admin.ui.empty-state
                title="No questions yet"
                copy="Add the first question to build the list."
            />
        @else
            <ul class="admin-sections">
                @foreach ($items as $item)
                    <li class="admin-sections__item">
                        <div class="admin-sections__body">
                            <span class="admin-sections__index">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>

                            <p class="admin-sections__name">{{ $item->translation($editLocale)?->question ?? '—' }}</p>

                            <span class="admin-pill{{ $item->is_active ? ' admin-pill--on' : '' }}">
                                {{ $item->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="admin-sections__actions">
                            {{-- Order is changed a step at a time, so it never
                                 depends on a script being available. --}}
                            <form method="POST" action="{{ route('admin.content.faq.move', [$page, $section, $item]) }}">
                                @csrf
                                <input type="hidden" name="direction" value="up">
                                <button type="submit" class="admin-button admin-button--quiet" @disabled($loop->first) aria-label="Move up">&uarr;</button>
                            </form>

                            <form method="POST" action="{{ route('admin.content.faq.move', [$page, $section, $item]) }}">
                                @csrf
                                <input type="hidden" name="direction" value="down">
                                <button type="submit" class="admin-button admin-button--quiet" @disabled($loop->last) aria-label="Move down">&darr;</button>
                            </form>

                            <a class="admin-button admin-button--ghost" href="{{ route('admin.content.faq.edit', [$page, $section, $item]) }}">Edit</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="admin-panel">
        <a class="admin-button admin-button--quiet" href="{{ route('admin.content.section.edit', [$page, $section]) }}">Back to section</a>
    </div>

@endsection
