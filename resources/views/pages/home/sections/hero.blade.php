@php
    /* Content and imagery come from the CMS; the markup is unchanged. */
    $heroImage = $s->media('hero_image');
@endphp

<section class="home-hero">
    <div class="home-hero__media" aria-hidden="true">
        @if ($s->hasImage('hero_image'))
            <img
                src="{{ $s->imageUrl('hero_image') }}"
                alt=""
                class="home-hero__image"
                @if ($heroImage?->width) width="{{ $heroImage->width }}" height="{{ $heroImage->height }}" @endif
                loading="eager"
                fetchpriority="high"
            >
        @endif
    </div>

    <div class="home-hero__overlay" aria-hidden="true"></div>

    <div class="lux-container home-hero__container">
        <div class="home-hero__content">
            <h1 class="home-hero__title">
                <span class="home-hero__title-line">{{ $s->text('title_1') }}</span>
                <span class="home-hero__title-line">{{ $s->text('title_2') }}</span>
                <span class="home-hero__title-line">{{ $s->text('title_3') }}</span>
                <span class="home-hero__title-line">{{ $s->text('title_4') }}</span>
            </h1>

            <span class="home-hero__accent" aria-hidden="true"></span>

            <p class="home-hero__description">
                {{ $s->text('description') }}
            </p>

            <div class="home-hero__actions">
                <a href="{{ $s->link('cta_route') }}" class="home-hero__cta">
                    <span>{{ $s->text('cta') }}</span>
                    <span class="home-hero__cta-icon" aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</section>
