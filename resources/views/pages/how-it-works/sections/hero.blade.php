@php
    /* A chapter index into the process below: the step titles come from that
       section, so they are never entered twice. */
    $processSection = $page->section('process');

    $heroIndex = $processSection ? [
        ['number' => '01', 'label' => $processSection->text('steps.join.title')],
        ['number' => '02', 'label' => $processSection->text('steps.book.title')],
        ['number' => '03', 'label' => $processSection->text('steps.use.title')],
    ] : [];
@endphp

<section class="hiw-hero">
    <div class="hiw-hero__media">
        @if ($s->hasImage('hero_image'))
            <img
                src="{{ $s->imageUrl('hero_image') }}"
                alt="{{ $s->text('image_alt') }}"
                class="hiw-hero__image"
                loading="eager"
                fetchpriority="high"
            >
        @endif
    </div>

    <div class="hiw-hero__overlay" aria-hidden="true"></div>

    <div class="lux-container hiw-hero__container">
        <div class="hiw-hero__content">
            <p class="hiw-hero__eyebrow" data-enter>{{ $s->text('eyebrow') }}</p>

            <h1 class="hiw-hero__title" data-enter data-enter-delay="1">
                <span class="hiw-hero__title-line">{{ $s->text('title_1') }}</span>
                <span class="hiw-hero__title-line">{{ $s->text('title_2') }}</span>
            </h1>

            <p class="hiw-hero__copy" data-enter data-enter-delay="2">
                {{ $s->text('copy') }}
            </p>
        </div>

        {{-- The rule and its spacing sit on the wrapper because the global
             ul[class]/ol[class] reset out-specifies a class on the list. --}}
        @if ($heroIndex !== [])
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
        @endif
    </div>
</section>
