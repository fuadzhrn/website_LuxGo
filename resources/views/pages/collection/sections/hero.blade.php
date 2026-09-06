@php
    $heroImage = 'assets/images/luxgo/collection/hero/collection-hero.webp';
@endphp

{{-- Replace with collection-hero.webp in public/assets/images/luxgo/collection/hero/.
     Until the file exists the section keeps its dark media slot instead of
     rendering a broken image. --}}

<section class="collection-hero">
    <div class="collection-hero__media">
        @if (file_exists(public_path($heroImage)))
            <img
                src="{{ asset($heroImage) }}"
                alt="{{ __('collection.hero.image_alt') }}"
                class="collection-hero__image"
                loading="eager"
                fetchpriority="high"
            >
        @endif
    </div>

    <div class="collection-hero__overlay" aria-hidden="true"></div>

    <div class="lux-container collection-hero__container">
        <div class="collection-hero__content">
            <p class="collection-hero__eyebrow" data-enter>{{ __('collection.hero.eyebrow') }}</p>

            <h1 class="collection-hero__title" data-enter data-enter-delay="1">
                <span class="collection-hero__title-line">{{ __('collection.hero.title_1') }}</span>
                <span class="collection-hero__title-line">{{ __('collection.hero.title_2') }}</span>
                <span class="collection-hero__title-line">{{ __('collection.hero.title_3') }}</span>
            </h1>

            <p class="collection-hero__copy" data-enter data-enter-delay="2">
                {{ __('collection.hero.copy') }}
            </p>

            <a href="#featured-vehicle" class="collection-hero__link" data-enter data-enter-delay="3">
                <span>{{ __('collection.hero.link') }}</span>
                <span class="collection-hero__link-icon" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>
