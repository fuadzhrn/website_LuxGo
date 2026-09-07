@extends('admin.layouts.app')

@section('title', $definition['label'])

@php
    $locales = config('locales.supported');
    $localeLabels = config('admin.locale_labels');

    /* Fields keep their definition order; a group only exists to give repeated
       items a heading, never to let the admin add or remove one. */
    $grouped = collect($definition['fields'] ?? [])->groupBy(fn ($field) => $field['group'] ?? '', preserveKeys: true);

    $fieldName = fn (string $locale, string $path) => 'content['.$locale.']['.implode('][', explode('.', $path)).']';
@endphp

@section('content')

    <nav class="admin-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.content') }}">Content</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('admin.content.page', $page) }}">{{ config('page_content.pages.'.$page->key.'.label') }}</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{{ $definition['label'] }}</span>
    </nav>

    <x-admin.ui.section-header
        :title="$definition['label']"
        description="Text is entered per language. Images, the CTA destination and the status are shared, so they are set once."
    />

    <form
        method="POST"
        action="{{ route('admin.content.section.update', [$page, $section]) }}"
        enctype="multipart/form-data"
    >
        @csrf
        @method('PUT')

        <div class="admin-panel">
            <x-admin.content.language-tabs>
                @foreach ($locales as $index => $locale)
                    <x-admin.content.language-panel :locale="$locale" :active="$index === 0">
                        @foreach ($grouped as $group => $fields)
                            @if ($group !== '')
                                <p class="admin-fieldgroup">{{ $group }}</p>
                            @endif

                            @foreach ($fields as $path => $field)
                                @php($name = $fieldName($locale, $path))
                                @php($current = data_get($content[$locale] ?? [], $path))

                                @if (($field['type'] ?? 'text') === 'textarea')
                                    <x-admin.form.textarea
                                        :name="$name"
                                        :label="$field['label']"
                                        :value="$current"
                                        :help="$field['help'] ?? null"
                                        :rows="3"
                                    />
                                @else
                                    <x-admin.form.input
                                        :name="$name"
                                        :label="$field['label']"
                                        :value="$current"
                                        :help="$field['help'] ?? null"
                                    />
                                @endif
                            @endforeach
                        @endforeach
                    </x-admin.content.language-panel>
                @endforeach
            </x-admin.content.language-tabs>
        </div>

        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Shared"
                description="The same in {{ implode(' and ', array_values($localeLabels)) }} — an image is never uploaded twice."
            />

            @foreach ($definition['media'] ?? [] as $slot => $slotDefinition)
                <x-admin.content.image-field
                    :name="'media_'.$slot"
                    :label="$slotDefinition['label']"
                    :media="$media[$slot] ?? null"
                    :help="isset($slotDefinition['alt'])
                        ? 'JPG, PNG or WebP. The alt text for this image is edited per language above.'
                        : 'JPG, PNG or WebP. This image is decorative and needs no alt text.'"
                />
            @endforeach

            @foreach ($definition['settings'] ?? [] as $key => $setting)
                @if (($setting['type'] ?? null) === 'vehicle')
                    {{-- A reference, not a copy: the section shows whichever
                         vehicle is chosen, with that vehicle's own content. --}}
                    <x-admin.form.select
                        :name="'settings['.$key.']'"
                        :label="$setting['label']"
                        :options="App\Models\Vehicle::orderBy('sort_order')->orderBy('id')->pluck('name', 'id')->all()"
                        :value="$section->settings[$key] ?? null"
                        :help="$setting['help'] ?? null"
                    />
                @elseif (($setting['type'] ?? null) === 'cta')
                    <x-admin.form.select
                        :name="'settings['.$key.']'"
                        :label="$setting['label']"
                        :options="collect(config('page_content.cta_targets'))->map(fn ($target) => $target['label'])->all()"
                        :value="$section->settings[$key] ?? ($setting['default'] ?? null)"
                        help="The link keeps the visitor's language automatically."
                    />
                @endif
            @endforeach

            <x-admin.content.status-toggle
                :checked="$section->is_active"
                help="An inactive section keeps its content but is not shown on the public page."
            />
        </div>

        @if ($definition['faq'] ?? false)
            <div class="admin-panel">
                <x-admin.ui.section-header
                    title="FAQ"
                    description="The questions in this section are managed on their own screen, so they can be added, reordered and switched off individually."
                />

                <a class="admin-button admin-button--ghost" href="{{ route('admin.content.faq', [$page, $section]) }}">Manage FAQ</a>
            </div>
        @endif

        <x-admin.ui.save-bar :updated-at="$section->updated_at">
            <a class="admin-button admin-button--quiet" href="{{ route('admin.content.page', $page) }}">Back to sections</a>
        </x-admin.ui.save-bar>
    </form>

@endsection
