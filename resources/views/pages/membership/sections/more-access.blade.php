@php
    $lotExamples = [
        ['lots' => '1 LOT', 'rights' => '6×'],
        ['lots' => '5 LOT', 'rights' => '14×'],
        ['lots' => '10 LOT', 'rights' => '24×'],
    ];
@endphp

<section class="membership-access">
    <div class="lux-container membership-access__inner">
        <div class="membership-access__intro">
            <p class="membership-access__eyebrow">Membership Access</p>

            <h2 class="membership-access__title">
                <span class="membership-access__title-line">More LOT.</span>
                <span class="membership-access__title-line">More Access.</span>
            </h2>

            <p class="membership-access__copy">
                Start with 6 Usage Rights per year with 1 LOT. Every additional LOT adds 2 more Usage Rights per year.
            </p>

            <div class="membership-access__rule">
                <div class="membership-access__rule-item">
                    <p class="membership-access__rule-label">1 LOT</p>
                    <p class="membership-access__rule-value">6<span class="membership-access__times">×</span></p>
                    <p class="membership-access__rule-unit">/ Year</p>
                </div>

                <div class="membership-access__rule-item">
                    <p class="membership-access__rule-label">Each Additional LOT</p>
                    <p class="membership-access__rule-value">+2<span class="membership-access__times">×</span></p>
                    <p class="membership-access__rule-unit">/ Year</p>
                </div>
            </div>

            <div class="membership-access__examples">
                @foreach ($lotExamples as $example)
                    <div class="membership-access__example">
                        <p class="membership-access__example-lots">{{ $example['lots'] }}</p>
                        <p class="membership-access__example-rights">{{ $example['rights'] }}</p>
                        <p class="membership-access__example-unit">/ Year</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="membership-access__calculator" data-calculator>
            <p class="membership-access__calculator-title">Plan Your Access</p>
            <p class="membership-access__calculator-copy">
                Choose the number of LOTs to see your available Usage Rights.
            </p>

            <div class="membership-access__stepper">
                <button
                    type="button"
                    class="membership-access__step"
                    data-calculator-decrease
                    aria-label="Decrease LOT"
                    disabled
                >&minus;</button>

                <p class="membership-access__stepper-value">
                    <span data-calculator-lots>1</span>
                    <span class="membership-access__stepper-unit">LOT</span>
                </p>

                <button
                    type="button"
                    class="membership-access__step"
                    data-calculator-increase
                    aria-label="Increase LOT"
                >+</button>
            </div>

            <div class="membership-access__results" aria-live="polite">
                <div class="membership-access__result">
                    <p class="membership-access__result-value" data-calculator-annual>6×</p>
                    <p class="membership-access__result-label">Usage Rights / Year</p>
                </div>

                <div class="membership-access__result">
                    <p class="membership-access__result-value" data-calculator-total>30×</p>
                    <p class="membership-access__result-label">Total Usage Rights / 5 Years</p>
                </div>
            </div>

            <p class="membership-access__calculator-note">
                Each additional LOT adds 2 Usage Rights per year. Additional LOT pricing follows applicable membership terms.
            </p>
        </div>
    </div>
</section>
