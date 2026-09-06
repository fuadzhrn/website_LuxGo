@php
    $membershipMetrics = [
        [
            'value' => '5',
            'unit' => __('membership.hero.unit_years'),
            'label' => __('membership.hero.label_period'),
            'accent' => false,
        ],
        [
            'value' => '6',
            'unit' => __('membership.hero.unit_per_year'),
            'label' => __('membership.hero.label_rights'),
            'accent' => true,
        ],
        [
            'value' => '30',
            'unit' => __('membership.hero.unit_per_five_years'),
            'label' => __('membership.hero.label_total_rights'),
            'accent' => true,
        ],
    ];
@endphp

<section class="membership-section membership-hero">
    <div class="lux-container membership-hero__inner">
        <div class="membership-hero__content">
            <p class="membership-hero__eyebrow" data-enter>{{ __('membership.hero.eyebrow') }}</p>

            <h1 class="membership-hero__title" data-enter data-enter-delay="1">
                <span class="membership-hero__title-line">{{ __('membership.hero.title_1') }}</span>
                <span class="membership-hero__title-line">{{ __('membership.hero.title_2') }}</span>
                <span class="membership-hero__title-line">{{ __('membership.hero.title_3') }}</span>
            </h1>

            <p class="membership-hero__copy" data-enter data-enter-delay="2">
                {{ __('membership.hero.copy') }}
            </p>
        </div>

        <div class="membership-hero__panel" data-enter data-enter-delay="1">
            <p class="membership-hero__panel-label">{{ __('membership.hero.panel_label') }}</p>

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
