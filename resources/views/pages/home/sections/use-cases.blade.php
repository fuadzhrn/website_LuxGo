@php
    $useCases = [
        [
            'label' => __('home.use_cases.business.label'),
            'image' => 'assets/images/luxgo/home/use-cases/business.webp',
            'alt' => __('home.use_cases.business.alt'),
            'lines' => [__('home.use_cases.business.line_1'), __('home.use_cases.business.line_2')],
        ],
        [
            'label' => __('home.use_cases.family.label'),
            'image' => 'assets/images/luxgo/home/use-cases/family.webp',
            'alt' => __('home.use_cases.family.alt'),
            'lines' => [__('home.use_cases.family.line_1'), __('home.use_cases.family.line_2')],
        ],
        [
            'label' => __('home.use_cases.life.label'),
            'image' => 'assets/images/luxgo/home/use-cases/life.webp',
            'alt' => __('home.use_cases.life.alt'),
            'lines' => [__('home.use_cases.life.line_1'), __('home.use_cases.life.line_2')],
        ],
    ];
@endphp

{{-- Photography goes in public/assets/images/luxgo/home/use-cases/ as
     business.webp, family.webp and life.webp. Until a file is present the card
     keeps its dark media slot instead of rendering a broken image. --}}

<section class="home-section home-use-cases">
    <div class="lux-container">
        <div class="home-use-cases__header">
            <div class="home-use-cases__intro-heading" data-reveal>
                <p class="home-use-cases__eyebrow lux-eyebrow">{{ __('home.use_cases.eyebrow') }}</p>
                <h2 class="home-use-cases__title">
                    <span class="home-use-cases__title-line">{{ __('home.use_cases.title_1') }}</span>
                    <span class="home-use-cases__title-line">{{ __('home.use_cases.title_2') }}</span>
                    <span class="home-use-cases__title-line">{{ __('home.use_cases.title_3') }}</span>
                </h2>
            </div>

            <p class="home-use-cases__description" data-reveal data-reveal-delay="1">
                {{ __('home.use_cases.description') }}
            </p>
        </div>

        <div class="home-use-cases__grid">
            @foreach ($useCases as $index => $useCase)
                <article class="home-use-case" data-reveal data-reveal-delay="{{ $index + 1 }}">
                    <div class="home-use-case__media">
                        @if (file_exists(public_path($useCase['image'])))
                            <img
                                src="{{ asset($useCase['image']) }}"
                                alt="{{ $useCase['alt'] }}"
                                class="home-use-case__image"
                                loading="lazy"
                            >
                        @endif

                        <div class="home-use-case__overlay">
                            <span class="home-use-case__accent" aria-hidden="true"></span>
                            <h3 class="home-use-case__label">{{ $useCase['label'] }}</h3>
                            <p class="home-use-case__copy">
                                @foreach ($useCase['lines'] as $line)
                                    <span class="home-use-case__copy-line">{{ $line }}</span>
                                @endforeach
                            </p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
