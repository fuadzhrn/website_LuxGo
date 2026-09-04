@php
    $membershipMetrics = [
        [
            'value' => '5',
            'unit' => 'Years',
            'label' => 'Membership Period',
            'accent' => false,
        ],
        [
            'value' => '6',
            'unit' => '/ Year',
            'label' => 'Usage Rights',
            'accent' => true,
        ],
        [
            'value' => '30',
            'unit' => '/ 5 Years',
            'label' => 'Total Usage Rights',
            'accent' => true,
        ],
    ];
@endphp

<section class="membership-section membership-hero">
    <div class="lux-container membership-hero__inner">
        <div class="membership-hero__content">
            <p class="membership-hero__eyebrow">Membership</p>

            <h1 class="membership-hero__title">
                <span class="membership-hero__title-line">One Membership.</span>
                <span class="membership-hero__title-line">Five Years of</span>
                <span class="membership-hero__title-line">Premium Access.</span>
            </h1>

            <p class="membership-hero__copy">
                Premium mobility through a five-year membership designed for access, flexibility, and convenience.
            </p>
        </div>

        <div class="membership-hero__panel">
            <p class="membership-hero__panel-label">Membership at a Glance</p>

            <div class="membership-hero__metrics">
                @foreach ($membershipMetrics as $metric)
                    <div class="membership-hero__metric">
                        <p class="membership-hero__value">
                            {{ $metric['value'] }}@if ($metric['accent'])<span class="membership-hero__times" aria-hidden="true">&times;</span>@endif
                        </p>
                        <p class="membership-hero__unit">{{ $metric['unit'] }}</p>
                        <p class="membership-hero__metric-label">{{ $metric['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
