@php
    $serviceAreas = [
        ['region' => 'Jakarta', 'areas' => ['Central', 'North', 'South', 'West', 'East']],
        ['region' => 'Tangerang', 'areas' => ['Kota Tangerang', 'Tangerang Selatan', 'Kabupaten Tangerang']],
        ['region' => 'Bekasi', 'areas' => ['Kota Bekasi', 'Kabupaten Bekasi']],
        ['region' => 'Bogor', 'areas' => ['Kota Bogor', 'Kabupaten Bogor']],
        ['region' => 'Depok', 'areas' => ['Kota Depok']],
    ];
@endphp

<section class="hiw-section hiw-area" id="service-area">
    <div class="lux-container hiw-area__inner">
        <div class="hiw-area__intro" data-reveal>
            <p class="hiw-area__eyebrow">Service Area</p>

            <h2 class="hiw-area__title">
                <span class="hiw-area__title-line">Serving</span>
                <span class="hiw-area__title-line">Jabodetabek.</span>
            </h2>

            <p class="hiw-area__copy">
                Premium mobility across the greater Jakarta area.
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
