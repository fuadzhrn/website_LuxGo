@php
    $driverImage = 'assets/images/luxgo/experience/driver/driver-service.webp';

    $driverAttributes = [
        __('experience.driver.attributes.appearance'),
        __('experience.driver.attributes.punctual'),
        __('experience.driver.attributes.polite'),
        __('experience.driver.attributes.defensive'),
        __('experience.driver.attributes.customer'),
        __('experience.driver.attributes.hospitality'),
        __('experience.driver.attributes.privacy'),
    ];
@endphp

{{-- Replace with driver-service.webp in public/assets/images/luxgo/experience/driver/.
     Until the file exists the column keeps its neutral media slot. --}}

<section class="experience-section experience-driver">
    <div class="lux-container experience-driver__inner">
        <figure class="experience-driver__media" data-reveal>
            @if (file_exists(public_path($driverImage)))
                <img
                    src="{{ asset($driverImage) }}"
                    alt="{{ __('experience.driver.image_alt') }}"
                    class="experience-driver__image"
                    loading="lazy"
                >
            @endif
        </figure>

        <div class="experience-driver__content" data-reveal data-reveal-delay="1">
            <h2 class="experience-driver__title">
                <span class="experience-driver__title-line">{{ __('experience.driver.title_1') }}</span>
                <span class="experience-driver__title-line">{{ __('experience.driver.title_2') }}</span>
            </h2>

            <p class="experience-driver__copy">
                {{ __('experience.driver.copy') }}
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
