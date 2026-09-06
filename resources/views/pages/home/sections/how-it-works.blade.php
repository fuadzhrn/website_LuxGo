@php
    $processSteps = [
        [
            'number' => '01',
            'icon' => 'user-round.svg',
            'title' => __('home.how.steps.join.title'),
            'copy' => __('home.how.steps.join.copy'),
        ],
        [
            'number' => '02',
            'icon' => 'calendar.svg',
            'title' => __('home.how.steps.book.title'),
            'copy' => __('home.how.steps.book.copy'),
        ],
        [
            'number' => '03',
            'icon' => 'car-front.svg',
            'title' => __('home.how.steps.use.title'),
            'copy' => __('home.how.steps.use.copy'),
        ],
    ];
@endphp

<section class="home-section home-how">
    <div class="lux-container home-how__inner">
        <div class="home-how__process">
            <p class="home-how__eyebrow" data-reveal>{{ __('home.how.eyebrow') }}</p>

            <h2 class="home-how__title" data-reveal data-reveal-delay="1">{{ __('home.how.title') }}</h2>

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
                <span class="home-how__cta-title-line">{{ __('home.how.cta_title_1') }}</span>
                <span class="home-how__cta-title-line">{{ __('home.how.cta_title_2') }}</span>
            </h3>

            <p class="home-how__cta-copy" data-reveal data-reveal-delay="1">
                {{ __('home.how.cta_copy') }}
            </p>

            <a href="{{ route('membership') }}" class="home-how__cta-link">
                <span>{{ __('home.how.cta_link') }}</span>
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
