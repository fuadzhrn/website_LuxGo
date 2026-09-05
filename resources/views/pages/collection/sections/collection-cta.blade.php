@php
    $ctaImage = 'assets/images/luxgo/collection/cta/collection-cta-detail.webp';
@endphp

{{-- Replace with collection-cta-detail.webp in public/assets/images/luxgo/collection/cta/. --}}

<section class="collection-cta">
    <div class="collection-cta__media" aria-hidden="true">
        @if (file_exists(public_path($ctaImage)))
            <img
                src="{{ asset($ctaImage) }}"
                alt=""
                class="collection-cta__image"
                loading="lazy"
            >
        @endif
    </div>

    <div class="lux-container collection-cta__inner" data-reveal>
        <div class="collection-cta__content">
            <h2 class="collection-cta__title">
                <span class="collection-cta__title-line">Your Premium</span>
                <span class="collection-cta__title-line">Journey Starts</span>
                <span class="collection-cta__title-line">Here.</span>
            </h2>

            <p class="collection-cta__copy">
                Premium mobility, designed around the moments that matter.
            </p>

            <a href="{{ route('membership') }}" class="collection-cta__link">
                <span>Explore Membership</span>
                <span class="collection-cta__link-icon" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>
