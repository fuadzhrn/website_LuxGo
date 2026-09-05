@php
    $packageMetrics = [
        ['value' => '5', 'unit' => 'Years', 'label' => 'Membership Period'],
        ['value' => '6×', 'unit' => '/ Year', 'label' => 'Usage Rights'],
        ['value' => '30×', 'unit' => '/ 5 Years', 'label' => 'Total Usage Rights'],
    ];
@endphp

<section class="membership-package">
    <div class="lux-container membership-package__inner">
        <div class="membership-package__intro" data-reveal>
            <p class="membership-package__eyebrow">Membership Package</p>

            <h2 class="membership-package__title">
                <span class="membership-package__title-line">One LOT.</span>
                <span class="membership-package__title-line">Five Years of Access.</span>
            </h2>

            <p class="membership-package__copy">
                One LOT is the basis of a LUX&amp;GO Membership. It runs for five years and gives you a set of Usage Rights every year.
            </p>

            <p class="membership-package__lot">
                <span class="membership-package__lot-number">01</span>
                <span class="membership-package__lot-label">LOT Membership</span>
            </p>
        </div>

        <div class="membership-package__offer" data-reveal data-reveal-delay="1">
            <div class="membership-package__block">
                <p class="membership-package__block-label">Membership Fee</p>

                <div class="membership-package__prices">
                    <div class="membership-package__price membership-package__price--regular">
                        <p class="membership-package__price-caption">Regular Membership</p>
                        <p class="membership-package__price-value">Rp35.000.000</p>
                    </div>

                    <div class="membership-package__price membership-package__price--promo">
                        <p class="membership-package__price-caption">Special Promo</p>
                        <p class="membership-package__price-value">Rp25.000.000</p>
                        <p class="membership-package__price-note">First 100 Members</p>
                    </div>
                </div>
            </div>

            <div class="membership-package__block">
                <p class="membership-package__block-label">What You Get</p>

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
                <p class="membership-package__block-label">Member Usage Fee</p>

                <p class="membership-package__usage">
                    <span class="membership-package__usage-value">Rp750.000</span>
                    <span class="membership-package__usage-unit">/ 12 Hours</span>
                </p>

                <p class="membership-package__usage-note">
                    Charged per use, separately from the membership fee. Professional Driver included.
                </p>
            </div>

            <a href="/become-a-member" class="membership-package__cta">
                <span>Become a Member</span>
                <span class="membership-package__cta-icon" aria-hidden="true">&rarr;</span>
            </a>
        </div>
    </div>
</section>
