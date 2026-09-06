@php
    $interiorMain = 'assets/images/luxgo/collection/interior/interior-main.webp';
    $interiorDetails = [
        ['path' => 'assets/images/luxgo/collection/interior/interior-detail-01.webp', 'alt' => __('collection.inside.detail_1_alt')],
        ['path' => 'assets/images/luxgo/collection/interior/interior-detail-02.webp', 'alt' => __('collection.inside.detail_2_alt')],
    ];
@endphp

{{-- Replace with interior-main.webp, interior-detail-01.webp and interior-detail-02.webp
     in public/assets/images/luxgo/collection/interior/. Empty slots stay dark
     instead of rendering broken images. --}}

<section class="collection-inside">
    <div class="lux-container">
        <div class="collection-inside__header" data-reveal>
            <div class="collection-inside__heading">
                <p class="collection-inside__eyebrow">{{ __('collection.inside.eyebrow') }}</p>

                <h2 class="collection-inside__title">
                    <span class="collection-inside__title-line">{{ __('collection.inside.title_1') }}</span>
                    <span class="collection-inside__title-line">{{ __('collection.inside.title_2') }}</span>
                    <span class="collection-inside__title-line">{{ __('collection.inside.title_3') }}</span>
                </h2>
            </div>

            <p class="collection-inside__copy">
                {{ __('collection.inside.copy') }}
            </p>
        </div>

        <div class="collection-inside__gallery" data-reveal data-reveal-delay="1">
            <figure class="collection-inside__main">
                @if (file_exists(public_path($interiorMain)))
                    <img
                        src="{{ asset($interiorMain) }}"
                        alt="{{ __('collection.inside.main_alt') }}"
                        class="collection-inside__image"
                        loading="lazy"
                    >
                @endif
            </figure>

            <div class="collection-inside__details">
                @foreach ($interiorDetails as $detail)
                    <figure class="collection-inside__detail">
                        @if (file_exists(public_path($detail['path'])))
                            <img
                                src="{{ asset($detail['path']) }}"
                                alt="{{ $detail['alt'] }}"
                                class="collection-inside__image"
                                loading="lazy"
                            >
                        @endif
                    </figure>
                @endforeach
            </div>
        </div>
    </div>
</section>
