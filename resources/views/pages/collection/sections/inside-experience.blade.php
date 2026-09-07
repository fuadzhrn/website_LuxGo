@php
    /* The interior belongs to a vehicle, so the images come from that vehicle's
       gallery rather than being copied into this section. The first three
       images fill the main slot and the two details. */
    $galleryVehicle = $vehicles->firstWhere('id', (int) ($s->setting('vehicle_id') ?? 0)) ?? $vehicles->first();
    $gallery = $galleryVehicle?->galleryMedia ?? collect();

    $interiorMain = $gallery->get(0);
    $interiorDetails = [
        ['media' => $gallery->get(1), 'alt' => $s->text('detail_1_alt')],
        ['media' => $gallery->get(2), 'alt' => $s->text('detail_2_alt')],
    ];
@endphp

<section class="collection-inside">
    <div class="lux-container">
        <div class="collection-inside__header" data-reveal>
            <div class="collection-inside__heading">
                <p class="collection-inside__eyebrow">{{ $s->text('eyebrow') }}</p>

                <h2 class="collection-inside__title">
                    <span class="collection-inside__title-line">{{ $s->text('title_1') }}</span>
                    <span class="collection-inside__title-line">{{ $s->text('title_2') }}</span>
                    <span class="collection-inside__title-line">{{ $s->text('title_3') }}</span>
                </h2>
            </div>

            <p class="collection-inside__copy">
                {{ $s->text('copy') }}
            </p>
        </div>

        <div class="collection-inside__gallery" data-reveal data-reveal-delay="1">
            <figure class="collection-inside__main">
                @if ($interiorMain?->exists())
                    <img
                        src="{{ $interiorMain->url() }}"
                        alt="{{ $s->text('main_alt') }}"
                        class="collection-inside__image"
                        loading="lazy"
                    >
                @endif
            </figure>

            <div class="collection-inside__details">
                @foreach ($interiorDetails as $detail)
                    <figure class="collection-inside__detail">
                        @if ($detail['media']?->exists())
                            <img
                                src="{{ $detail['media']->url() }}"
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
