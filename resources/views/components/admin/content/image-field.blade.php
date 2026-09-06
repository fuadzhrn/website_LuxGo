@props([
    'name',
    'label' => 'Image',
    'media' => null,
    'help' => null,
    'altName' => null,
    'altLabel' => 'Alt text',
    'altValue' => null,
])

@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = 'f-'.Str::slug(str_replace('.', '-', $key));
    $hasError = $errors->has($key);
    $accept = config('admin.images.accept');
    $maxKb = config('admin.images.max_kilobytes');
@endphp

{{-- Nothing here touches storage. Choosing a file only draws a preview, picking
     an existing image only records its id, and Remove just marks intent — all of
     it is resolved when the form is saved.

     Remove clears this field. It never deletes the file from the library. --}}
<div class="admin-image" data-admin-image>
    <p class="admin-label" id="{{ $id }}-label">{{ $label }}</p>

    <div class="admin-image__frame">
        @if ($media)
            <img
                class="admin-image__preview"
                src="{{ $media->url() }}"
                alt="{{ $media->alt_text ?? '' }}"
                data-image-preview
            >
        @else
            <img class="admin-image__preview" src="" alt="" hidden data-image-preview>
        @endif

        <p class="admin-image__placeholder" @if ($media) hidden @endif data-image-placeholder>
            No image selected
        </p>
    </div>

    <p class="admin-image__state" hidden data-image-state></p>

    <div class="admin-image__actions">
        <label class="admin-button admin-button--ghost" for="{{ $id }}">
            {{ $media ? 'Replace image' : 'Upload image' }}
        </label>

        <input
            class="admin-image__input"
            type="file"
            id="{{ $id }}"
            name="{{ $name }}"
            accept="{{ $accept }}"
            aria-labelledby="{{ $id }}-label"
            aria-describedby="{{ $id }}-help"
            @if ($hasError) aria-invalid="true" @endif
            data-image-input
        >

        <button type="button" class="admin-button admin-button--ghost" data-image-choose>
            Choose existing
        </button>

        <button type="button" class="admin-button admin-button--quiet" data-image-remove @unless ($media) hidden @endunless>
            Remove image
        </button>

        <button type="button" class="admin-button admin-button--quiet" data-image-undo hidden>
            Undo
        </button>
    </div>

    {{-- Read on save: an id when an existing image was picked, the remove flag
         when the field was cleared. The library itself is never modified here. --}}
    <input type="hidden" name="{{ $name }}_media_id" value="{{ old($key.'_media_id', $media?->id) }}" data-image-media-id>
    <input type="hidden" name="{{ $name }}_remove" value="0" data-image-remove-flag>

    <p class="admin-help" id="{{ $id }}-help">
        {{ $help ?? 'JPG, PNG or WebP. Up to '.round($maxKb / 1024).' MB.' }}
    </p>

    <x-admin.form.error :name="$name" :id="$id.'-error'" />

    @if ($altName)
        <div class="admin-image__alt">
            <x-admin.form.input :name="$altName" :label="$altLabel" :value="$altValue" />
        </div>
    @endif
</div>

@once
    @push('modals')
        @include('admin.partials.media-picker')
    @endpush
@endonce
