@php
    $driverImage = 'assets/images/luxgo/experience/driver/driver-service.webp';

    $driverAttributes = [
        'Professional Appearance',
        'Punctual',
        'Polite & Courteous',
        'Defensive Driving',
        'Customer-Oriented',
        'Hospitality Mindset',
        'Privacy & Confidentiality',
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
                    alt="A LUX&GO professional driver attending to a member."
                    class="experience-driver__image"
                    loading="lazy"
                >
            @endif
        </figure>

        <div class="experience-driver__content" data-reveal data-reveal-delay="1">
            <h2 class="experience-driver__title">
                <span class="experience-driver__title-line">Not Just</span>
                <span class="experience-driver__title-line">a Driver.</span>
            </h2>

            <p class="experience-driver__copy">
                Professional service that goes beyond getting you from one place to another.
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
