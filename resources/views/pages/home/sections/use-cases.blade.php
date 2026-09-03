@php
    $useCases = [
        [
            'label' => 'Business',
            'image' => 'assets/images/luxgo/home/use-cases/business.webp',
            'alt' => 'Premium mobility for business meetings and client visits.',
            'details' => ['Meeting · Client Visit', 'Business Trip · Site Visit'],
        ],
        [
            'label' => 'Family',
            'image' => 'assets/images/luxgo/home/use-cases/family.webp',
            'alt' => 'Premium mobility for family weekends and outings.',
            'details' => ['Weekend · Shopping', 'Dining · Family Activities'],
        ],
        [
            'label' => 'Life',
            'image' => 'assets/images/luxgo/home/use-cases/life.webp',
            'alt' => 'Premium mobility for special occasions and events.',
            'details' => ['Events · Wedding', 'Airport · Special Occasions'],
        ],
    ];
@endphp

{{-- Photography goes in public/assets/images/luxgo/home/use-cases/ as
     business.webp, family.webp and life.webp. Until a file is present the card
     keeps its empty media slot instead of rendering a broken image. --}}

<section class="home-section home-use-cases">
    <div class="lux-container">
        <div class="home-use-cases__header">
            <div class="home-use-cases__intro-heading" data-reveal>
                <p class="home-use-cases__eyebrow lux-eyebrow">Use It Your Way</p>
                <h2 class="home-use-cases__title">
                    <span class="home-use-cases__title-line">For Business.</span>
                    <span class="home-use-cases__title-line">For Family.</span>
                    <span class="home-use-cases__title-line">For Life.</span>
                </h2>
            </div>

            <p class="home-use-cases__description" data-reveal data-reveal-delay="1">
                Premium mobility that adapts to the moments that matter — from business commitments to family time and special occasions.
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
                    </div>

                    <div class="home-use-case__meta">
                        <span class="home-use-case__index">{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</span>
                        <h3 class="home-use-case__label">{{ $useCase['label'] }}</h3>
                        <p class="home-use-case__details">
                            @foreach ($useCase['details'] as $line)
                                <span class="home-use-case__details-line">{{ $line }}</span>
                            @endforeach
                        </p>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>
