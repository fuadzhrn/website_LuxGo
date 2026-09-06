@extends('admin.layouts.app')

@section('title', 'Media')

@section('content')

    <x-admin.ui.section-header
        title="Media library"
        description="Images uploaded here are available to every page editor. Design assets in public/assets are developer files and are not managed from this screen."
    />

    <div class="admin-panel">
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data" class="admin-media__upload">
            @csrf

            <div class="admin-field">
                <label class="admin-label" for="media-file">Upload media</label>

                <input
                    class="admin-input{{ $errors->has('file') ? ' has-error' : '' }}"
                    type="file"
                    id="media-file"
                    name="file"
                    accept="{{ config('admin.images.accept') }}"
                    required
                    @if ($errors->has('file')) aria-invalid="true" @endif
                    aria-describedby="media-file-help"
                >

                <p class="admin-help" id="media-file-help">
                    JPG, PNG or WebP. Up to {{ round(config('admin.images.max_kilobytes') / 1024) }} MB.
                </p>

                <x-admin.form.error name="file" />
            </div>

            <button type="submit" class="admin-button admin-button--primary" data-admin-submit>Upload</button>
        </form>
    </div>

    <div class="admin-panel">
        <form method="GET" action="{{ route('admin.media') }}" class="admin-media__search">
            <div class="admin-field">
                <label class="admin-label" for="media-search">Search filename</label>
                <input class="admin-input" type="search" id="media-search" name="q" value="{{ $search }}" placeholder="e.g. hero">
            </div>

            <button type="submit" class="admin-button admin-button--ghost">Search</button>

            @if ($search !== '')
                <a class="admin-button admin-button--quiet" href="{{ route('admin.media') }}">Clear</a>
            @endif
        </form>

        @if ($media->isEmpty())
            <x-admin.ui.empty-state
                title="No media uploaded yet"
                :copy="$search !== '' ? 'No file matches that search.' : 'Upload an image to start building the library.'"
            />
        @else
            <ul class="admin-media__grid">
                @foreach ($media as $item)
                    <li class="admin-media__item{{ $selected && $selected->is($item) ? ' is-selected' : '' }}">
                        <a
                            class="admin-media__link"
                            href="{{ route('admin.media', array_filter(['q' => $search ?: null, 'page' => $media->currentPage() > 1 ? $media->currentPage() : null, 'selected' => $item->id])) }}"
                            @if ($selected && $selected->is($item)) aria-current="true" @endif
                        >
                            <span class="admin-media__thumb">
                                @if ($item->exists())
                                    <img src="{{ $item->url() }}" alt="{{ $item->alt_text ?? '' }}" loading="lazy">
                                @else
                                    <span class="admin-media__missing">File missing</span>
                                @endif
                            </span>

                            <span class="admin-media__name">{{ $item->filename }}</span>
                            <span class="admin-media__meta">{{ strtoupper($item->extension) }} · {{ $item->dimensionsLabel() }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="admin-media__pagination">
                {{ $media->links() }}
            </div>
        @endif
    </div>

    @if ($selected)
        <div class="admin-panel" id="media-detail">
            <x-admin.ui.section-header title="Media detail" />

            <div class="admin-media__detail">
                <div class="admin-media__detail-preview">
                    @if ($selected->exists())
                        <img src="{{ $selected->url() }}" alt="{{ $selected->alt_text ?? '' }}">
                    @else
                        <p class="admin-help">The file for this record is missing from storage.</p>
                    @endif
                </div>

                <div class="admin-media__detail-body">
                    <dl class="admin-detail">
                        <div class="admin-detail__row">
                            <dt class="admin-detail__label">Filename</dt>
                            <dd class="admin-detail__value">{{ $selected->filename }}</dd>
                        </div>
                        <div class="admin-detail__row">
                            <dt class="admin-detail__label">Dimensions</dt>
                            <dd class="admin-detail__value">{{ $selected->dimensionsLabel() }}</dd>
                        </div>
                        <div class="admin-detail__row">
                            <dt class="admin-detail__label">File size</dt>
                            <dd class="admin-detail__value">{{ $selected->humanSize() }}</dd>
                        </div>
                        <div class="admin-detail__row">
                            <dt class="admin-detail__label">Type</dt>
                            <dd class="admin-detail__value">{{ $selected->mime_type }}</dd>
                        </div>
                        <div class="admin-detail__row">
                            <dt class="admin-detail__label">Uploaded</dt>
                            <dd class="admin-detail__value">{{ $selected->created_at?->format('d M Y, H:i') }}</dd>
                        </div>
                    </dl>

                    <form method="POST" action="{{ route('admin.media.update', $selected) }}" class="admin-media__alt">
                        @csrf
                        @method('PATCH')

                        <x-admin.form.input
                            name="alt_text"
                            label="Alt text"
                            :value="$selected->alt_text"
                            help="Describes the image for screen readers. Optional."
                        />

                        <button type="submit" class="admin-button admin-button--primary" data-admin-submit>Save alt text</button>
                    </form>

                    @php($usedBy = $selected->usedBy())

                    @if ($usedBy !== [])
                        {{-- Deleting is refused while anything still points at this row. --}}
                        <p class="admin-media__locked">
                            This media is currently in use and cannot be deleted.
                            <span class="admin-help">Referenced by: {{ implode(', ', $usedBy) }}.</span>
                        </p>
                    @else
                        {{-- A disclosure rather than a JS confirm, so the second
                             deliberate click is required with or without JS. --}}
                        <details class="admin-confirm">
                            <summary class="admin-confirm__summary">Delete media</summary>

                            <div class="admin-confirm__body">
                                <p class="admin-confirm__copy">Delete this media? This action cannot be undone.</p>

                                <form method="POST" action="{{ route('admin.media.destroy', $selected) }}">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="admin-button admin-button--danger">Delete</button>
                                    <a class="admin-button admin-button--quiet" href="{{ route('admin.media') }}">Cancel</a>
                                </form>
                            </div>
                        </details>
                    @endif
                </div>
            </div>
        </div>
    @endif

@endsection
