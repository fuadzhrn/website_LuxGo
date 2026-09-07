@php
    /* The service area as published: five regions, each with its own locations.
       The CMS holds their wording; the list itself is not something the editor
       can add to. */
    $serviceAreas = collect([
        ['key' => 'jakarta', 'locations' => ['central', 'north', 'south', 'west', 'east']],
        ['key' => 'tangerang', 'locations' => ['kota', 'selatan', 'kabupaten']],
        ['key' => 'bekasi', 'locations' => ['kota', 'kabupaten']],
        ['key' => 'bogor', 'locations' => ['kota', 'kabupaten']],
        ['key' => 'depok', 'locations' => ['kota']],
    ])->map(fn (array $area) => [
        'region' => $s->text("areas.{$area['key']}.name"),
        'areas' => array_values(array_filter(array_map(
            fn (string $location) => $s->text("areas.{$area['key']}.locations.{$location}"),
            $area['locations']
        ))),
    ]);
@endphp

<section class="hiw-section hiw-area" id="service-area">
    <div class="lux-container hiw-area__inner">
        <div class="hiw-area__intro" data-reveal>
            <p class="hiw-area__eyebrow">{{ $s->text('eyebrow') }}</p>

            <h2 class="hiw-area__title">
                <span class="hiw-area__title-line">{{ $s->text('title_1') }}</span>
                <span class="hiw-area__title-line">{{ $s->text('title_2') }}</span>
            </h2>

            <p class="hiw-area__copy">
                {{ $s->text('copy') }}
            </p>
        </div>

        {{-- Five regions as hairline-separated rows — never five cards. --}}
        <ul class="hiw-area__list" data-reveal data-reveal-delay="1">
            @foreach ($serviceAreas as $area)
                <li class="hiw-area__row">
                    <h3 class="hiw-area__region">{{ $area['region'] }}</h3>
                    <p class="hiw-area__detail">{{ implode(' · ', $area['areas']) }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>
