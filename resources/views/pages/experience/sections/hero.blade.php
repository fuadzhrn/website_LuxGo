@php
    $heroImage = 'assets/images/luxgo/experience/hero/experience-hero.webp';
@endphp

{{-- Replace with experience-hero.webp in public/assets/images/luxgo/experience/hero/.
     Until the file exists the section keeps its dark media slot. --}}

<section class="experience-hero">
    <div class="experience-hero__media">
        @if (file_exists(public_path($heroImage)))
            <img
                src="{{ asset($heroImage) }}"
                alt="A LUX&GO professional driver welcoming a member on arrival."
                class="experience-hero__image"
                loading="eager"
                fetchpriority="high"
            >
        @endif
    </div>

    <div class="experience-hero__overlay" aria-hidden="true"></div>

    <div class="lux-container experience-hero__container">
        <div class="experience-hero__content">
            <div class="experience-hero__heading">
                <p class="experience-hero__eyebrow" data-enter>The Experience</p>

                <h1 class="experience-hero__title" data-enter data-enter-delay="1">
                    <span class="experience-hero__title-line">Every Journey.</span>
                    <span class="experience-hero__title-line">Elevated.</span>
                </h1>
            </div>

            <div class="experience-hero__aside">
                <p class="experience-hero__copy" data-enter data-enter-delay="2">
                    Premium mobility shaped by thoughtful service, professional driving, and attention to every journey.
                </p>

                <a href="#the-service" class="experience-hero__link" data-enter data-enter-delay="3">
                    <span>Discover the Service</span>
                    <span class="experience-hero__link-icon" aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</section>
