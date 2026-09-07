<section class="experience-hero">
    <div class="experience-hero__media">
        @if ($s->hasImage('hero_image'))
            <img
                src="{{ $s->imageUrl('hero_image') }}"
                alt="{{ $s->text('image_alt') }}"
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
                <p class="experience-hero__eyebrow" data-enter>{{ $s->text('eyebrow') }}</p>

                <h1 class="experience-hero__title" data-enter data-enter-delay="1">
                    <span class="experience-hero__title-line">{{ $s->text('title_1') }}</span>
                    <span class="experience-hero__title-line">{{ $s->text('title_2') }}</span>
                </h1>
            </div>

            <div class="experience-hero__aside">
                <p class="experience-hero__copy" data-enter data-enter-delay="2">
                    {{ $s->text('copy') }}
                </p>

                {{-- An anchor into the section below, which is page structure
                     rather than something the CMS sets. --}}
                <a href="#the-service" class="experience-hero__link" data-enter data-enter-delay="3">
                    <span>{{ $s->text('link') }}</span>
                    <span class="experience-hero__link-icon" aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</section>
