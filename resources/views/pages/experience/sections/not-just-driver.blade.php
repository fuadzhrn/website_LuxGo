@php
    /* Seven attributes, fixed by the layout — the CMS supplies their wording. */
    $driverAttributes = [
        $s->text('attributes.appearance'),
        $s->text('attributes.punctual'),
        $s->text('attributes.polite'),
        $s->text('attributes.defensive'),
        $s->text('attributes.customer'),
        $s->text('attributes.hospitality'),
        $s->text('attributes.privacy'),
    ];
@endphp

<section class="experience-section experience-driver">
    <div class="lux-container experience-driver__inner">
        <figure class="experience-driver__media" data-reveal>
            @if ($s->hasImage('driver_image'))
                <img
                    src="{{ $s->imageUrl('driver_image') }}"
                    alt="{{ $s->text('image_alt') }}"
                    class="experience-driver__image"
                    loading="lazy"
                >
            @endif
        </figure>

        <div class="experience-driver__content" data-reveal data-reveal-delay="1">
            <h2 class="experience-driver__title">
                <span class="experience-driver__title-line">{{ $s->text('title_1') }}</span>
                <span class="experience-driver__title-line">{{ $s->text('title_2') }}</span>
            </h2>

            <p class="experience-driver__copy">
                {{ $s->text('copy') }}
            </p>

            <ul class="experience-driver__list">
                @foreach ($driverAttributes as $index => $attribute)
                    <li class="experience-driver__item">
                        <span class="experience-driver__number">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <span class="experience-driver__label">{{ $attribute }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
