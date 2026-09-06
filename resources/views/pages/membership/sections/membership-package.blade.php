@php
    /* The same three key numbers as the hero, from the same single source. */
    $packageMetrics = [
        ['value' => (string) $membership->periodYears(), 'unit' => $s->text('unit_years'), 'label' => $s->text('label_period')],
        ['value' => $membership->baseRights().'×', 'unit' => $s->text('unit_per_year'), 'label' => $s->text('label_rights')],
        ['value' => $membership->totalMembershipRights().'×', 'unit' => $s->text('unit_per_five_years'), 'label' => $s->text('label_total_rights')],
    ];
@endphp

<section class="membership-package">
    <div class="lux-container membership-package__inner">
        <div class="membership-package__intro" data-reveal>
            <p class="membership-package__eyebrow">{{ $s->text('eyebrow') }}</p>

            <h2 class="membership-package__title">
                <span class="membership-package__title-line">{{ $s->text('title_1') }}</span>
                <span class="membership-package__title-line">{{ $s->text('title_2') }}</span>
            </h2>

            <p class="membership-package__copy">
                {{ $s->text('copy') }}
            </p>

            <p class="membership-package__lot">
                <span class="membership-package__lot-number">01</span>
                <span class="membership-package__lot-label">{{ $s->text('lot_label') }}</span>
            </p>
        </div>

        <div class="membership-package__offer" data-reveal data-reveal-delay="1">
            <div class="membership-package__block">
                <p class="membership-package__block-label">{{ $s->text('fee_label') }}</p>

                <div class="membership-package__prices">
                    <div class="membership-package__price membership-package__price--regular">
                        <p class="membership-package__price-caption">{{ $s->text('price_regular') }}</p>
                        <p class="membership-package__price-value">{{ $membership->regularPrice() }}</p>
                    </div>

                    <div class="membership-package__price membership-package__price--promo">
                        <p class="membership-package__price-caption">{{ $s->text('price_promo') }}</p>
                        <p class="membership-package__price-value">{{ $membership->promoPrice() }}</p>
                        <p class="membership-package__price-note">{{ $s->text('price_note') }}</p>
                    </div>
                </div>
            </div>

            <div class="membership-package__block">
                <p class="membership-package__block-label">{{ $s->text('what_you_get') }}</p>

                <div class="membership-package__metrics">
                    @foreach ($packageMetrics as $metric)
                        <div class="membership-package__metric">
                            <p class="membership-package__metric-value">{{ $metric['value'] }}</p>
                            <p class="membership-package__metric-unit">{{ $metric['unit'] }}</p>
                            <p class="membership-package__metric-label">{{ $metric['label'] }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="membership-package__block membership-package__block--usage">
                <p class="membership-package__block-label">{{ $s->text('usage_label') }}</p>

                <p class="membership-package__usage">
                    <span class="membership-package__usage-value">{{ $membership->memberUsageFee() }}</span>
                    <span class="membership-package__usage-unit">{{ $s->text('usage_unit') }}</span>
                </p>

                <p class="membership-package__usage-note">
                    {{ $s->text('usage_note') }}
                </p>
            </div>

            <a href="{{ $s->link('cta_target') }}" class="membership-package__cta">
                <span>{{ __('global.cta.become_member') }}</span>
                <span class="membership-package__cta-icon" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>
