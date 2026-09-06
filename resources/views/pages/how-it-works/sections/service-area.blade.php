@php
    $serviceAreas = [
        ['region' => 'Jakarta', 'areas' => [__('how-it-works.area.jakarta.central'), __('how-it-works.area.jakarta.north'), __('how-it-works.area.jakarta.south'), __('how-it-works.area.jakarta.west'), __('how-it-works.area.jakarta.east')]],
        ['region' => 'Tangerang', 'areas' => ['Kota Tangerang', 'Tangerang Selatan', 'Kabupaten Tangerang']],
        ['region' => 'Bekasi', 'areas' => ['Kota Bekasi', 'Kabupaten Bekasi']],
        ['region' => 'Bogor', 'areas' => ['Kota Bogor', 'Kabupaten Bogor']],
        ['region' => 'Depok', 'areas' => ['Kota Depok']],
    ];
@endphp

<section class="hiw-section hiw-area" id="service-area">
    <div class="lux-container hiw-area__inner">
        <div class="hiw-area__intro" data-reveal>
            <p class="hiw-area__eyebrow">{{ __('how-it-works.area.eyebrow') }}</p>

            <h2 class="hiw-area__title">
                <span class="hiw-area__title-line">{{ __('how-it-works.area.title_1') }}</span>
                <span class="hiw-area__title-line">{{ __('how-it-works.area.title_2') }}</span>
            </h2>

            <p class="hiw-area__copy">
                {{ __('how-it-works.area.copy') }}
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
