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
                alt="The Denza D9, the vehicle behind the LUX&GO experience."
                class="collection-hero__image"
                loading="eager"
                fetchpriority="high"
            >
        @endif
    </div>

    <div class="collection-hero__overlay" aria-hidden="true"></div>

    <div class="lux-container collection-hero__container">
        <div class="collection-hero__content">
            <p class="collection-hero__eyebrow" data-enter>Our Collection</p>

            <h1 class="collection-hero__title" data-enter data-enter-delay="1">
                <span class="collection-hero__title-line">The Collection.</span>
                <span class="collection-hero__title-line">Designed For</span>
                <span class="collection-hero__title-line">Premium Mobility.</span>
            </h1>

            <p class="collection-hero__copy" data-enter data-enter-delay="2">
                Discover the vehicle behind the LUX&amp;GO experience.
            </p>

            <a href="#featured-vehicle" class="collection-hero__link" data-enter data-enter-delay="3">
                <span>Discover Denza D9</span>
                <span class="collection-hero__link-icon" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>
