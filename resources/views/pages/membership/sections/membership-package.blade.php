@php
    $packageMetrics = [
        ['value' => '5', 'unit' => __('membership.package.unit_years'), 'label' => __('membership.package.label_period')],
        ['value' => '6×', 'unit' => __('membership.package.unit_per_year'), 'label' => __('membership.package.label_rights')],
        ['value' => '30×', 'unit' => __('membership.package.unit_per_five_years'), 'label' => __('membership.package.label_total_rights')],
    ];
@endphp

<section class="membership-package">
    <div class="lux-container membership-package__inner">
        <div class="membership-package__intro" data-reveal>
            <p class="membership-package__eyebrow">{{ __('membership.package.eyebrow') }}</p>

            <h2 class="membership-package__title">
                <span class="membership-package__title-line">{{ __('membership.package.title_1') }}</span>
                <span class="membership-package__title-line">{{ __('membership.package.title_2') }}</span>
            </h2>

            <p class="membership-package__copy">
                {{ __('membership.package.copy') }}
            </p>

            <p class="membership-package__lot">
                <span class="membership-package__lot-number">01</span>
                <span class="membership-package__lot-label">{{ __('membership.package.lot_label') }}</span>
            </p>
        </div>

        <div class="membership-package__offer" data-reveal data-reveal-delay="1">
            <div class="membership-package__block">
                <p class="membership-package__block-label">{{ __('membership.package.fee_label') }}</p>

                <div class="membership-package__prices">
                    <div class="membership-package__price membership-package__price--regular">
                        <p class="membership-package__price-caption">{{ __('membership.package.price_regular') }}</p>
                        <p class="membership-package__price-value">Rp35.000.000</p>
                    </div>

                    <div class="membership-package__price membership-package__price--promo">
                        <p class="membership-package__price-caption">{{ __('membership.package.price_promo') }}</p>
                        <p class="membership-package__price-value">Rp25.000.000</p>
                        <p class="membership-package__price-note">{{ __('membership.package.price_note') }}</p>
                    </div>
                </div>
            </div>

            <div class="membership-package__block">
                <p class="membership-package__block-label">{{ __('membership.package.what_you_get') }}</p>

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
                <p class="membership-package__block-label">{{ __('membership.package.usage_label') }}</p>

                <p class="membership-package__usage">
                    <span class="membership-package__usage-value">Rp750.000</span>
                    <span class="membership-package__usage-unit">{{ __('membership.package.usage_unit') }}</span>
                </p>

                <p class="membership-package__usage-note">
                    {{ __('membership.package.usage_note') }}
                </p>
            </div>

            <a href="/become-a-member" class="membership-package__cta">
                <span>{{ __('global.cta.become_member') }}</span>
                <span class="membership-package__cta-icon" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>
