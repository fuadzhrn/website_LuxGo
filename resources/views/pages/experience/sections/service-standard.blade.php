@php
    $servicePillars = [
        [
            'number' => '01',
            'title' => __('experience.standard.pillars.professional.title'),
            'copy' => __('experience.standard.pillars.professional.copy'),
        ],
        [
            'number' => '02',
            'title' => __('experience.standard.pillars.hospitality.title'),
            'copy' => __('experience.standard.pillars.hospitality.copy'),
        ],
        [
            'number' => '03',
            'title' => __('experience.standard.pillars.privacy.title'),
            'copy' => __('experience.standard.pillars.privacy.copy'),
        ],
    ];
@endphp

<section class="experience-standard" id="the-service">
    <div class="lux-container">
        <div class="experience-standard__header" data-reveal>
            <div class="experience-standard__heading">
                <p class="experience-standard__eyebrow">{{ __('experience.standard.eyebrow') }}</p>

                <h2 class="experience-standard__title">
                    <span class="experience-standard__title-line">{{ __('experience.standard.title_1') }}</span>
                    <span class="experience-standard__title-line">{{ __('experience.standard.title_2') }}</span>
                </h2>
            </div>
        </div>

        <div class="experience-standard__pillars" data-reveal data-reveal-delay="1">
            @foreach ($servicePillars as $pillar)
                <div class="experience-standard__pillar">
                    <p class="experience-standard__number">{{ $pillar['number'] }}</p>
                    <h3 class="experience-standard__pillar-title">{{ $pillar['title'] }}</h3>
                    <p class="experience-standard__pillar-copy">{{ $pillar['copy'] }}</p>
                </div>
            @endforeach
        </div>

    </div>

    <div class="experience-standard__cta">
        <div class="lux-container experience-standard__cta-inner" data-reveal>
            <div class="experience-standard__cta-content">
                <h2 class="experience-standard__cta-title">
                    <span class="experience-standard__title-line">{{ __('experience.standard.cta_title_1') }}</span>
                    <span class="experience-standard__title-line">{{ __('experience.standard.cta_title_2') }}</span>
                </h2>

                <p class="experience-standard__cta-copy">
                    {{ __('experience.standard.cta_copy') }}
                </p>
            </div>

            <a href="{{ route('membership') }}" class="experience-standard__link">
                <span>{{ __('global.cta.become_member') }}</span>
                <span class="experience-standard__link-icon" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>
