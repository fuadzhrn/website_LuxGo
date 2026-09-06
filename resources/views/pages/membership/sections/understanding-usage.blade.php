<section class="membership-usage">
    <div class="lux-container">
        <div class="membership-usage__header" data-reveal>
            <div class="membership-usage__heading">
                <p class="membership-usage__eyebrow">{{ __('membership.usage.eyebrow') }}</p>

                <h2 class="membership-usage__title">
                    <span class="membership-usage__title-line">{{ __('membership.usage.title_1') }}</span>
                    <span class="membership-usage__title-line">{{ __('membership.usage.title_2') }}</span>
                </h2>
            </div>

            <p class="membership-usage__copy">
                {{ __('membership.usage.copy') }}
            </p>
        </div>

        <div class="membership-usage__comparison" data-reveal data-reveal-delay="1">
            <div class="membership-usage__case">
                <p class="membership-usage__case-label">{{ __('membership.usage.with_rights') }}</p>

                <p class="membership-usage__amount">Rp750.000</p>
                <p class="membership-usage__unit">{{ __('membership.usage.unit') }}</p>
                <p class="membership-usage__caption">{{ __('membership.usage.caption') }}</p>

                <p class="membership-usage__note">{{ __('membership.usage.driver_included') }}</p>
            </div>

            <span class="membership-usage__divider" aria-hidden="true"></span>

            <div class="membership-usage__case">
                <p class="membership-usage__case-label">{{ __('membership.usage.after_rights') }}</p>

                <div class="membership-usage__breakdown">
                    <div class="membership-usage__row">
                        <span class="membership-usage__row-label">{{ __('membership.usage.regular_usage') }}</span>
                        <span class="membership-usage__row-value">Rp750.000</span>
                    </div>

                    <div class="membership-usage__row">
                        <span class="membership-usage__row-label">{{ __('membership.usage.additional_usage') }}</span>
                        <span class="membership-usage__row-value">+ Rp500.000</span>
                    </div>

                    <div class="membership-usage__row membership-usage__row--total">
                        <span class="membership-usage__row-label">{{ __('membership.usage.total') }}</span>
                        <span class="membership-usage__amount">Rp1.250.000</span>
                    </div>
                </div>

                <p class="membership-usage__unit">{{ __('membership.usage.unit') }}</p>

                <p class="membership-usage__note">{{ __('membership.usage.availability') }}</p>
            </div>
        </div>
    </div>
</section>
