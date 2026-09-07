@extends('admin.layouts.app')

@section('title', 'SEO')

@php($localeLabels = config('admin.locale_labels'))

@section('content')

    <x-admin.ui.section-header
        title="SEO"
        description="Manage search engine and social sharing information for each website page."
    />

    <div class="admin-panel">
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th scope="col">Page</th>
                        @foreach (config('locales.supported') as $code)
                            <th scope="col">{{ $localeLabels[$code] ?? strtoupper($code) }}</th>
                        @endforeach
                        <th scope="col">Search engines</th>
                        <th scope="col"><span class="admin-visually-hidden">Actions</span></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pages as $page)
                        @php($setting = $page->seoSetting)
                        <tr>
                            <td>{{ config('page_content.pages.'.$page->key.'.label', Str::headline($page->key)) }}</td>

                            @foreach (config('locales.supported') as $code)
                                <td>
                                    {{-- Read from the records themselves, never assumed. --}}
                                    <x-admin.ui.status-badge
                                        :status="$seo->isComplete($setting, $code) ? 'completed' : 'new'"
                                        :label="$seo->isComplete($setting, $code) ? 'Complete' : 'Missing'"
                                    />
                                </td>
                            @endforeach

                            <td>
                                <x-admin.ui.status-badge
                                    :status="($setting?->is_indexable ?? true) ? 'completed' : 'rejected'"
                                    :label="($setting?->is_indexable ?? true) ? 'Indexable' : 'Not indexable'"
                                />
                            </td>

                            <td class="admin-table__actions">
                                <a class="admin-button admin-button--ghost" href="{{ route('admin.seo.edit', $page) }}">Edit SEO</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
