@extends('admin.layouts.app')

@php
    $isNew = ! $vehicle->exists;
    $locales = config('locales.supported');
    $grouped = collect($fields)->groupBy(fn ($field) => $field['group'] ?? '', preserveKeys: true);
    $fieldName = fn (string $locale, string $path) => 'content['.$locale.']['.implode('][', explode('.', $path)).']';
    $gallery = $isNew ? collect() : $vehicle->galleryMedia()->get();
@endphp

@section('title', $isNew ? 'Add vehicle' : $vehicle->name)

@section('content')

    <nav class="admin-breadcrumb" aria-label="Breadcrumb">
        <a href="{{ route('admin.content') }}">Content</a>
        <span aria-hidden="true">/</span>
        <a href="{{ route('admin.vehicles') }}">Vehicles</a>
        <span aria-hidden="true">/</span>
        <span aria-current="page">{{ $isNew ? 'Add vehicle' : $vehicle->name }}</span>
    </nav>

    <x-admin.ui.section-header
        :title="$isNew ? 'Add vehicle' : $vehicle->name"
        description="The name, slug, imagery, status and order are shared. Only the copy is entered per language."
    />

    <form
        method="POST"
        action="{{ $isNew ? route('admin.vehicles.store') : route('admin.vehicles.update', $vehicle) }}"
        enctype="multipart/form-data"
    >
        @csrf
        @unless ($isNew)
            @method('PUT')
        @endunless

        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Vehicle details"
                description="The name is a proper noun and is the same in both languages."
            />

            <x-admin.form.input name="name" label="Vehicle name" :value="$vehicle->name" required />

            <x-admin.form.input
                name="slug"
                label="Slug"
                :value="$vehicle->slug"
                help="Lowercase letters, numbers and hyphens. Used in links to this vehicle."
                required
            />
        </div>

        <div class="admin-panel">
            <x-admin.content.language-tabs>
                @foreach ($locales as $index => $locale)
                    <x-admin.content.language-panel :locale="$locale" :active="$index === 0">
                        @foreach ($grouped as $group => $groupFields)
                            @if ($group !== '')
                                <p class="admin-fieldgroup">{{ $group }}</p>
                            @endif

                            @foreach ($groupFields as $path => $field)
                                @php($name = $fieldName($locale, $path))
                                @php($current = data_get($vehicle->translation($locale)?->content ?? [], $path))

                                @if (($field['type'] ?? 'text') === 'textarea')
                                    <x-admin.form.textarea :name="$name" :label="$field['label']" :value="$current" :rows="3" />
                                @else
                                    <x-admin.form.input :name="$name" :label="$field['label']" :value="$current" />
                                @endif
                            @endforeach
                        @endforeach
                    </x-admin.content.language-panel>
                @endforeach
            </x-admin.content.language-tabs>
        </div>

        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Main image"
                description="Shown in the vehicle showcase. The same image in both languages; its alt text is entered per language above."
            />

            <x-admin.content.image-field
                name="media_main_image"
                label="Main image"
                :media="$vehicle->mainMedia"
                help="JPG, PNG or WebP. Removing it here does not delete it from the media library."
            />
        </div>

        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Status and order"
                description="An inactive vehicle keeps everything it has; it is simply not shown on the public page."
            />

            <x-admin.content.status-toggle :checked="$vehicle->isActive()" />

            <x-admin.form.input
                name="sort_order"
                label="Order"
                type="number"
                :value="$vehicle->sort_order ?? 0"
                min="0"
                step="1"
                help="Lower numbers come first. The list screen has arrows for this too."
                required
            />
        </div>

        <x-admin.ui.save-bar :updated-at="$vehicle->exists ? $vehicle->updated_at : null">
            <a class="admin-button admin-button--quiet" href="{{ route('admin.vehicles') }}">Back to vehicles</a>
        </x-admin.ui.save-bar>
    </form>

    @unless ($isNew)
        {{-- The gallery is managed a step at a time so its order is always the
             order the database holds, with or without JavaScript. --}}
        <div class="admin-panel">
            <x-admin.ui.section-header
                title="Gallery"
                description="The interior images this vehicle shows. Removing an image here only detaches it — it stays in the media library."
            />

            @if ($gallery->isEmpty())
                <p class="admin-help">No images in this gallery yet.</p>
            @else
                <ul class="admin-gallery">
                    @foreach ($gallery as $media)
                        <li class="admin-gallery__item">
                            <span class="admin-gallery__thumb">
                                @if ($media->exists())
                                    <img src="{{ $media->url() }}" alt="" loading="lazy">
                                @else
                                    <span class="admin-media__missing">File missing</span>
                                @endif
                            </span>

                            <span class="admin-gallery__name">{{ $media->filename }}</span>

                            <span class="admin-gallery__actions">
                                <form method="POST" action="{{ route('admin.vehicles.gallery.move', [$vehicle, $media->id]) }}">
                                    @csrf
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="admin-button admin-button--quiet" @disabled($loop->first) aria-label="Move up">&uarr;</button>
                                </form>

                                <form method="POST" action="{{ route('admin.vehicles.gallery.move', [$vehicle, $media->id]) }}">
                                    @csrf
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="admin-button admin-button--quiet" @disabled($loop->last) aria-label="Move down">&darr;</button>
                                </form>

                                <form method="POST" action="{{ route('admin.vehicles.gallery.destroy', [$vehicle, $media->id]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="admin-button admin-button--quiet">Remove</button>
                                </form>
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif

            <form
                method="POST"
                action="{{ route('admin.vehicles.gallery.store', $vehicle) }}"
                enctype="multipart/form-data"
                class="admin-gallery__add"
            >
                @csrf

                <div class="admin-field">
                    <label class="admin-label" for="gallery-file">Add an image</label>

                    <input
                        class="admin-input"
                        type="file"
                        id="gallery-file"
                        name="file"
                        accept="{{ config('admin.images.accept') }}"
                        aria-describedby="gallery-file-help"
                    >

                    <p class="admin-help" id="gallery-file-help">
                        Upload a new image, or pick one already in the library below.
                    </p>

                    <x-admin.form.error name="file" />
                </div>

                <x-admin.form.select
                    name="media_id"
                    label="…or choose from the media library"
                    :options="['' => '— none —'] + $libraryMedia"
                />

                <button type="submit" class="admin-button admin-button--ghost" data-admin-submit>Add to gallery</button>
            </form>
        </div>

        <div class="admin-panel">
            @if ($vehicle->isInUse())
                {{-- Refused while a section still points at this vehicle. --}}
                <p class="admin-media__locked">
                    This vehicle is currently in use. Deactivate it or change the referenced vehicle before deleting.
                    <span class="admin-help">Referenced by: {{ implode(', ', $vehicle->usedBy()) }}.</span>
                </p>
            @else
                <details class="admin-confirm">
                    <summary class="admin-confirm__summary">Delete vehicle</summary>

                    <div class="admin-confirm__body">
                        <p class="admin-confirm__copy">
                            Delete this vehicle and its content? Its images stay in the media library. This action cannot be undone.
                        </p>

                        <form method="POST" action="{{ route('admin.vehicles.destroy', $vehicle) }}">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="admin-button admin-button--danger">Delete</button>
                            <a class="admin-button admin-button--quiet" href="{{ route('admin.vehicles') }}">Cancel</a>
                        </form>
                    </div>
                </details>
            @endif
        </div>
    @endunless

@endsection
