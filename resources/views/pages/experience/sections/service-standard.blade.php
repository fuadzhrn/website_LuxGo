@php
    $servicePillars = [
        [
            'number' => '01',
            'title' => 'Professional',
            'copy' => 'Professional appearance, punctuality, and defensive driving.',
        ],
        [
            'number' => '02',
            'title' => 'Hospitality',
            'copy' => 'Polite, courteous, customer-oriented service with a hospitality mindset.',
        ],
        [
            'number' => '03',
            'title' => 'Privacy',
            'copy' => 'Privacy and confidentiality throughout the journey.',
        ],
    ];
@endphp

<section class="experience-standard" id="the-service">
    <div class="lux-container">
        <div class="experience-standard__header" data-reveal>
            <div class="experience-standard__heading">
                <p class="experience-standard__eyebrow">LUX&amp;GO Service Standard</p>

                <h2 class="experience-standard__title">
                    <span class="experience-standard__title-line">Service,</span>
                    <span class="experience-standard__title-line">Without Compromise.</span>
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

        <div class="experience-standard__cta" data-reveal>
            <div class="experience-standard__cta-content">
                <h2 class="experience-standard__cta-title">
                    <span class="experience-standard__title-line">Experience</span>
                    <span class="experience-standard__title-line">LUX&amp;GO For Yourself.</span>
                </h2>

                <p class="experience-standard__cta-copy">
                    Premium mobility, professionally delivered.
                </p>
            </div>

            <a href="{{ route('membership') }}" class="experience-standard__link">
                <span>Become a Member</span>
                <span class="experience-standard__link-icon" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>
