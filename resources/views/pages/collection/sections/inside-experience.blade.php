@php
    $interiorMain = 'assets/images/luxgo/collection/interior/interior-main.webp';
    $interiorDetails = [
        ['path' => 'assets/images/luxgo/collection/interior/interior-detail-01.webp', 'alt' => 'Denza D9 interior detail.'],
        ['path' => 'assets/images/luxgo/collection/interior/interior-detail-02.webp', 'alt' => 'Denza D9 cabin detail.'],
    ];
@endphp

{{-- Replace with interior-main.webp, interior-detail-01.webp and interior-detail-02.webp
     in public/assets/images/luxgo/collection/interior/. Empty slots stay dark
     instead of rendering broken images. --}}

<section class="collection-inside">
    <div class="lux-container">
        <div class="collection-inside__header" data-reveal>
            <div class="collection-inside__heading">
                <p class="collection-inside__eyebrow">Inside the Experience</p>

                <h2 class="collection-inside__title">
                    <span class="collection-inside__title-line">Step Inside.</span>
                    <span class="collection-inside__title-line">Comfort in</span>
                    <span class="collection-inside__title-line">Every Detail.</span>
                </h2>
            </div>

            <p class="collection-inside__copy">
                A premium environment designed to make every journey feel composed and effortless.
            </p>
        </div>

        <div class="collection-inside__gallery" data-reveal data-reveal-delay="1">
            <figure class="collection-inside__main">
                @if (file_exists(public_path($interiorMain)))
                    <img
                        src="{{ asset($interiorMain) }}"
                        alt="Denza D9 interior."
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
