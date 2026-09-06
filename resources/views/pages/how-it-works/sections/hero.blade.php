@php
    $heroImage = 'assets/images/luxgo/how-it-works/hero/how-it-works-hero.webp';

    $heroIndex = [
        ['number' => '01', 'label' => __('how-it-works.process.steps.join.title')],
        ['number' => '02', 'label' => __('how-it-works.process.steps.book.title')],
        ['number' => '03', 'label' => __('how-it-works.process.steps.use.title')],
    ];
@endphp

<section class="hiw-hero">
    <div class="hiw-hero__media">
        @if (file_exists(public_path($heroImage)))
            <img
                src="{{ asset($heroImage) }}"
                alt="{{ __('how-it-works.hero.image_alt') }}"
                class="hiw-hero__image"
                loading="eager"
                fetchpriority="high"
            >
        @endif
    </div>

    <div class="hiw-hero__overlay" aria-hidden="true"></div>

    <div class="lux-container hiw-hero__container">
        <div class="hiw-hero__content">
            <p class="hiw-hero__eyebrow" data-enter>{{ __('how-it-works.hero.eyebrow') }}</p>

            <h1 class="hiw-hero__title" data-enter data-enter-delay="1">
                <span class="hiw-hero__title-line">{{ __('how-it-works.hero.title_1') }}</span>
                <span class="hiw-hero__title-line">{{ __('how-it-works.hero.title_2') }}</span>
            </h1>

            <p class="hiw-hero__copy" data-enter data-enter-delay="2">
                {{ __('how-it-works.hero.copy') }}
            </p>
        </div>

        {{-- A chapter index into the process below, not the process itself.
             The rule and its spacing sit on the wrapper because the global
             ul[class]/ol[class] reset out-specifies a class on the list. --}}
        <div class="hiw-hero__index" data-enter data-enter-delay="3">
            <ol class="hiw-hero__index-list">
                @foreach ($heroIndex as $entry)
                    <li class="hiw-hero__index-item">
                        <span class="hiw-hero__index-number">{{ $entry['number'] }}</span>
                        <span class="hiw-hero__index-label">{{ $entry['label'] }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>
