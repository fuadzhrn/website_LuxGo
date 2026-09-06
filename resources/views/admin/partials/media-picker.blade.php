{{-- One picker serves every image field on the page: the field that opened it
     receives the choice. Selecting stores a media id — no file is copied. --}}

<div class="admin-picker" id="admin-media-picker" data-media-picker hidden>
    <div class="admin-picker__scrim" data-picker-close></div>

    <div class="admin-picker__panel" role="dialog" aria-modal="true" aria-labelledby="admin-picker-title">
        <div class="admin-picker__head">
            <h2 class="admin-picker__title" id="admin-picker-title">Choose existing media</h2>

            <button type="button" class="admin-picker__close" data-picker-close aria-label="Close media picker">&times;</button>
        </div>

        <div class="admin-picker__body">
            @if ($pickerMedia->isEmpty())
                <p class="admin-help">No media in the library yet.</p>
            @else
                <ul class="admin-picker__grid">
                    @foreach ($pickerMedia as $item)
                        <li>
                            <button
                                type="button"
                                class="admin-picker__item"
                                data-picker-select
                                data-media-id="{{ $item->id }}"
                                data-media-url="{{ $item->exists() ? $item->url() : '' }}"
                                data-media-name="{{ $item->filename }}"
                                data-media-alt="{{ $item->alt_text ?? '' }}"
                            >
                                <span class="admin-picker__thumb">
                                    @if ($item->exists())
                                        <img src="{{ $item->url() }}" alt="" loading="lazy">
                                    @else
                                        <span class="admin-media__missing">File missing</span>
                                    @endif
                                </span>

                                <span class="admin-picker__name">{{ $item->filename }}</span>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="admin-picker__foot">
            {{-- Uploading is the library's job; sending the admin there keeps one
                 upload path instead of two. --}}
            <a class="admin-button admin-button--ghost" href="{{ route('admin.media') }}">Upload new in Media Library</a>

            <button type="button" class="admin-button admin-button--quiet" data-picker-close>Cancel</button>
        </div>
    </div>
</div>
