@php
    /* Labels come from the CMS, figures from the membership settings — the
       total is worked out, never stored. */
    $membershipMetrics = [
        [
            'value' => (string) $membership->periodYears(),
            'unit' => $s->text('unit_years'),
            'label' => $s->text('label_period'),
            'accent' => false,
        ],
        [
            'value' => (string) $membership->baseRights(),
            'unit' => $s->text('unit_per_year'),
            'label' => $s->text('label_rights'),
            'accent' => true,
        ],
        [
            'value' => (string) $membership->totalMembershipRights(),
            'unit' => $s->text('unit_per_five_years'),
            'label' => $s->text('label_total_rights'),
            'accent' => true,
        ],
    ];
@endphp

<section class="membership-section membership-hero">
    <div class="lux-container membership-hero__inner">
        <div class="membership-hero__content">
            <p class="membership-hero__eyebrow" data-enter>{{ $s->text('eyebrow') }}</p>

            <h1 class="membership-hero__title" data-enter data-enter-delay="1">
                <span class="membership-hero__title-line">{{ $s->text('title_1') }}</span>
                <span class="membership-hero__title-line">{{ $s->text('title_2') }}</span>
                <span class="membership-hero__title-line">{{ $s->text('title_3') }}</span>
            </h1>

            <p class="membership-hero__copy" data-enter data-enter-delay="2">
                {{ $s->text('copy') }}
            </p>
        </div>

        <div class="membership-hero__panel" data-enter data-enter-delay="1">
            <p class="membership-hero__panel-label">{{ $s->text('panel_label') }}</p>

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
