<section class="membership-usage">
    <div class="lux-container">
        <div class="membership-usage__header" data-reveal>
            <div class="membership-usage__heading">
                <p class="membership-usage__eyebrow">{{ $s->text('eyebrow') }}</p>

                <h2 class="membership-usage__title">
                    <span class="membership-usage__title-line">{{ $s->text('title_1') }}</span>
                    <span class="membership-usage__title-line">{{ $s->text('title_2') }}</span>
                </h2>
            </div>

            <p class="membership-usage__copy">
                {{ $s->text('copy') }}
            </p>
        </div>

        <div class="membership-usage__comparison" data-reveal data-reveal-delay="1">
            <div class="membership-usage__case">
                <p class="membership-usage__case-label">{{ $s->text('with_rights') }}</p>

                <p class="membership-usage__amount">{{ $membership->memberUsageFee() }}</p>
                <p class="membership-usage__unit">{{ $s->text('unit') }}</p>
                <p class="membership-usage__caption">{{ $s->text('caption') }}</p>

                <p class="membership-usage__note">{{ $s->text('driver_included') }}</p>
            </div>

            <span class="membership-usage__divider" aria-hidden="true"></span>

            <div class="membership-usage__case">
                <p class="membership-usage__case-label">{{ $s->text('after_rights') }}</p>

                {{-- The total is the sum of the two fees above it, worked out
                     rather than stored. --}}
                <div class="membership-usage__breakdown">
                    <div class="membership-usage__row">
                        <span class="membership-usage__row-label">{{ $s->text('regular_usage') }}</span>
                        <span class="membership-usage__row-value">{{ $membership->memberUsageFee() }}</span>
                    </div>

                    <div class="membership-usage__row">
                        <span class="membership-usage__row-label">{{ $s->text('additional_usage') }}</span>
                        <span class="membership-usage__row-value">+ {{ $membership->additionalUsageFee() }}</span>
                    </div>

                    <div class="membership-usage__row membership-usage__row--total">
                        <span class="membership-usage__row-label">{{ $s->text('total') }}</span>
                        <span class="membership-usage__amount">{{ $membership->additionalUsageTotalFormatted() }}</span>
                    </div>
                </div>

                <p class="membership-usage__unit">{{ $s->text('unit') }}</p>

                <p class="membership-usage__note">{{ $s->text('availability') }}</p>
            </div>
        </div>
    </div>
</section>
