@php
    /* Three cards, fixed by the layout. Each one has its own image slot, shared
       between the languages; only the copy and alt text are translated. */
    $useCases = [
        [
            'slot' => 'business_image',
            'label' => $s->text('business.label'),
            'alt' => $s->text('business.alt'),
            'lines' => [$s->text('business.line_1'), $s->text('business.line_2')],
        ],
        [
            'slot' => 'family_image',
            'label' => $s->text('family.label'),
            'alt' => $s->text('family.alt'),
            'lines' => [$s->text('family.line_1'), $s->text('family.line_2')],
        ],
        [
            'slot' => 'life_image',
            'label' => $s->text('life.label'),
            'alt' => $s->text('life.alt'),
            'lines' => [$s->text('life.line_1'), $s->text('life.line_2')],
        ],
    ];
@endphp

{{-- A card whose slot has no image keeps its dark media panel instead of
     rendering a broken image. --}}

<section class="home-section home-use-cases">
    <div class="lux-container">
        <div class="home-use-cases__header">
            <div class="home-use-cases__intro-heading" data-reveal>
                <p class="home-use-cases__eyebrow lux-eyebrow">{{ $s->text('eyebrow') }}</p>
                <h2 class="home-use-cases__title">
                    <span class="home-use-cases__title-line">{{ $s->text('title_1') }}</span>
                    <span class="home-use-cases__title-line">{{ $s->text('title_2') }}</span>
                    <span class="home-use-cases__title-line">{{ $s->text('title_3') }}</span>
                </h2>
            </div>

            <p class="home-use-cases__description" data-reveal data-reveal-delay="1">
                {{ $s->text('description') }}
            </p>
        </div>

        <div class="home-use-cases__grid">
            @foreach ($useCases as $index => $useCase)
                <article class="home-use-case" data-reveal data-reveal-delay="{{ $index + 1 }}">
                    <div class="home-use-case__media">
                        @if ($s->hasImage($useCase['slot']))
                            <img
                                src="{{ $s->imageUrl($useCase['slot']) }}"
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
