@php
    $processSteps = [
        [
            'number' => '01',
            'icon' => 'user-round.svg',
            'title' => 'Join',
            'copy' => 'Choose the membership plan that fits your lifestyle.',
        ],
        [
            'number' => '02',
            'icon' => 'calendar.svg',
            'title' => 'Book',
            'copy' => 'Reserve your ride based on vehicle availability.',
        ],
        [
            'number' => '03',
            'icon' => 'car-front.svg',
            'title' => 'Use',
            'copy' => 'Enjoy 12 hours with our professional driver.',
        ],
    ];
@endphp

<section class="home-section home-how">
    <div class="lux-container home-how__inner">
        <div class="home-how__process">
            <p class="home-how__eyebrow" data-reveal>How It Works</p>

            <h2 class="home-how__title" data-reveal data-reveal-delay="1">Three Simple Steps</h2>

            <ol class="home-how__steps">
                @foreach ($processSteps as $index => $step)
                    <li class="home-how__step" data-reveal data-reveal-delay="{{ $index + 1 }}">
                        <span class="home-how__step-icon">
                            <img
                                src="{{ asset('assets/icons/luxgo/process/'.$step['icon']) }}"
                                alt=""
                                width="20"
                                height="20"
                                loading="lazy"
                            >
                        </span>

                        <span class="home-how__marker">
                            <span class="home-how__number">{{ $step['number'] }}</span>
                            @unless ($loop->last)
                                <span class="home-how__connector" aria-hidden="true"></span>
                            @endunless
                        </span>

                        <h3 class="home-how__step-title">{{ $step['title'] }}</h3>
                        <p class="home-how__step-copy">{{ $step['copy'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>

        <span class="home-how__divider" aria-hidden="true"></span>

        <div class="home-how__cta">
            <h3 class="home-how__cta-title" data-reveal>
                <span class="home-how__cta-title-line">Ready to Experience</span>
                <span class="home-how__cta-title-line">Premium Mobility?</span>
            </h3>

            <p class="home-how__cta-copy" data-reveal data-reveal-delay="1">
                Join LUX&amp;GO today and elevate the way you move.
            </p>

            <a href="/membership" class="home-how__cta-link">
                <span>View Membership Plans</span>
                <img
                    src="{{ asset('assets/icons/luxgo/process/arrow-right.svg') }}"
                    alt=""
                    class="home-how__cta-link-icon"
                    width="16"
                    height="16"
                    loading="lazy"
                >
            </a>
        </div>
    </div>
</section>
