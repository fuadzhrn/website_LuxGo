@extends('admin.layouts.app')

@php
    $pageLabel = config('page_content.pages.'.$page->key.'.label', Str::headline($page->key));
    $locales = config('locales.supported');
@endphp

@section('title', $pageLabel.' — SEO')

@section('content')

    <nav class="admin-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.seo') }}">SEO</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{{ $pageLabel }}</span>
    </nav>

    <x-admin.ui.section-header
        :title="$pageLabel.' — SEO'"
        description="The wording is entered per language. The share image and the search engine switch are shared by both."
    />

    <form method="POST" action="{{ route('admin.seo.update', $page) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="admin-panel">
            <x-admin.content.language-tabs>
                @foreach ($locales as $index => $locale)
                    <x-admin.content.language-panel :locale="$locale" :active="$index === 0">
                        <x-admin.form.input
                            :name="'seo['.$locale.'][meta_title]'"
                            label="SEO title"
                            :value="$translations[$locale]?->meta_title"
                            help="Recommended: around 50–60 characters."
                        />

                        <x-admin.form.textarea
                            :name="'seo['.$locale.'][meta_description]'"
                            label="Meta description"
                            :value="$translations[$locale]?->meta_description"
                            :rows="3"
                            help="Recommended: around 150–160 characters."
                        />

                        <p class="admin-fieldgroup">Social sharing</p>

                        <x-admin.form.input
                            :name="'seo['.$locale.'][og_title]'"
                            label="OG title"
                            :value="$translations[$locale]?->og_title"
                            help="Optional. Left empty, the SEO title is used."
                        />

                        <x-admin.form.textarea
                            :name="'seo['.$locale.'][og_description]'"
                            label="OG description"
                            :value="$translations[$locale]?->og_description"
                            :rows="3"
                            help="Optional. Left empty, the meta description is used."
                        />
                    </x-admin.content.language-panel>
                @endforeach
            </x-admin.content.language-tabs>
        </div>

        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Shared settings"
                description="The same in both languages."
            />

            <x-admin.content.image-field
                name="media_og_image"
                label="OG image"
                :media="$setting->ogMedia"
                help="Shown when the page is shared. Removing it here does not delete it from the media library."
            />

            <x-admin.content.status-toggle
                name="is_indexable"
                label="Search engine visibility"
                :checked="$setting->is_indexable"
                on-label="Indexable"
                off-label="Not indexable"
                help="Allow search engines to index this page."
            />
        </div>

        <x-admin.ui.save-bar :updated-at="$setting->updated_at">
            <a class="admin-button admin-button--quiet" href="{{ route('admin.seo') }}">Back to SEO</a>
        </x-admin.ui.save-bar>
    </form>

@endsection
