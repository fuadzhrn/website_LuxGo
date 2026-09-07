<section class="collection-hero">
    <div class="collection-hero__media">
        @if ($s->hasImage('hero_image'))
            <img
                src="{{ $s->imageUrl('hero_image') }}"
                alt="{{ $s->text('image_alt') }}"
                class="collection-hero__image"
                loading="eager"
                fetchpriority="high"
            >
        @endif
    </div>

    <div class="collection-hero__overlay" aria-hidden="true"></div>

    <div class="lux-container collection-hero__container">
        <div class="collection-hero__content">
            <p class="collection-hero__eyebrow" data-enter>{{ $s->text('eyebrow') }}</p>

            <h1 class="collection-hero__title" data-enter data-enter-delay="1">
                <span class="collection-hero__title-line">{{ $s->text('title_1') }}</span>
                <span class="collection-hero__title-line">{{ $s->text('title_2') }}</span>
                <span class="collection-hero__title-line">{{ $s->text('title_3') }}</span>
            </h1>

            <p class="collection-hero__copy" data-enter data-enter-delay="2">
                {{ $s->text('copy') }}
            </p>

            {{-- An anchor to the showcase below, which is part of the page
                 structure rather than something the CMS sets. --}}
            <a href="#featured-vehicle" class="collection-hero__link" data-enter data-enter-delay="3">
                <span>{{ $s->text('link') }}</span>
                <span class="collection-hero__link-icon" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>
