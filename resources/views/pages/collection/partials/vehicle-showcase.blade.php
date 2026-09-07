@php
    /* One vehicle, in the markup the approved page already uses. Extracting it
       is what lets a second vehicle appear without a second design. */
    $locale = app()->getLocale();
    $content = $vehicle->translation($locale)?->content
        ?? $vehicle->translation('en')?->content
        ?? [];

    /* A missing line falls back to English rather than rendering blank. */
    $fallback = $vehicle->translation('en')?->content ?? [];
    $line = fn (string $path) => (string) (data_get($content, $path) ?? data_get($fallback, $path) ?? '');

    $nameLines = $vehicle->nameLines();

    $vehicleFeatures = [
        ['icon' => 'gem.svg', 'label' => $line('features.design')],
        ['icon' => 'armchair.svg', 'label' => $line('features.comfort')],
        ['icon' => 'zap.svg', 'label' => $line('features.ev')],
        ['icon' => 'user-round.svg', 'label' => $line('features.executive')],
    ];

    $mainImage = $vehicle->mainMedia;
@endphp

<div class="collection-featured__header" data-reveal>
    <div class="collection-featured__heading">
        @if ($showEyebrow)
            <p class="collection-featured__eyebrow">{{ $s->text('eyebrow') }}</p>
        @endif

        <h2 class="collection-featured__title">
            @foreach ($nameLines as $nameLine)
                <span class="collection-featured__title-line">{{ $nameLine }}</span>
            @endforeach
        </h2>
    </div>

    <p class="collection-featured__copy">
        {{ $line('tagline') }}
    </p>
</div>

<div class="collection-featured__showcase" data-reveal data-reveal-delay="1">
    <div class="collection-featured__media">
        @if ($mainImage?->exists())
            <img
                src="{{ $mainImage->url() }}"
                alt="{{ $line('image_alt') }}"
                class="collection-featured__image"
                loading="lazy"
            >
        @endif
    </div>

    <div class="collection-featured__rail">
        <ul class="collection-featured__features">
            @foreach ($vehicleFeatures as $feature)
                <li class="collection-featured__feature">
                    <img
                        src="{{ asset('assets/icons/luxgo/collection/'.$feature['icon']) }}"
                        alt=""
                        class="collection-featured__feature-icon"
                        width="20"
                        height="20"
                        loading="lazy"
                    >
                    <span class="collection-featured__feature-label">{{ $feature['label'] }}</span>
                </li>
            @endforeach
        </ul>

        <a href="{{ $s->link('cta_target') }}" class="collection-featured__link">
            <span>{{ $s->text('link') }}</span>
            <span class="collection-featured__link-icon" aria-hidden="true">&rarr;</span>
        </a>
    </div>
</div>
