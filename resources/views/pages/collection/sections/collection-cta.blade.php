<section class="collection-cta">
    <div class="collection-cta__media" aria-hidden="true">
        @if ($s->hasImage('cta_image'))
            <img
                src="{{ $s->imageUrl('cta_image') }}"
                alt=""
                class="collection-cta__image"
                loading="lazy"
            >
        @endif
    </div>

    <div class="lux-container collection-cta__inner" data-reveal>
        <div class="collection-cta__content">
            <h2 class="collection-cta__title">
                <span class="collection-cta__title-line">{{ $s->text('title_1') }}</span>
                <span class="collection-cta__title-line">{{ $s->text('title_2') }}</span>
                <span class="collection-cta__title-line">{{ $s->text('title_3') }}</span>
            </h2>

            <p class="collection-cta__copy">
                {{ $s->text('copy') }}
            </p>

            <a href="{{ $s->link('cta_target') }}" class="collection-cta__link">
                <span>{{ $s->text('link') }}</span>
                <span class="collection-cta__link-icon" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>
