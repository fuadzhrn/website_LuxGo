@php
    /* The three worked examples the page shows. The LOT counts are the design;
       the figures come from the same rule the calculator uses, so an example can
       never disagree with it.

       The two benefits are reported separately: Usage Rights are the base and
       stay the same however many LOTs are held, while Discounted Usage Rights
       are what the additional LOTs buy. Adding them would produce a number that
       describes neither. */
    $lotExamples = collect([1, 5, 10])->map(fn (int $lots) => [
        'lots' => $lots.' LOT',
        'rights' => $membership->usageRightsPerYear().'×',
        'discounted' => $membership->discountedRightsFor($lots).'×',
    ]);
@endphp

<section class="membership-access">
    <div class="lux-container membership-access__inner">
        <div class="membership-access__intro" data-reveal>
            <p class="membership-access__eyebrow">{{ $s->text('eyebrow') }}</p>

            <h2 class="membership-access__title">
                <span class="membership-access__title-line">{{ $s->text('title_1') }}</span>
                <span class="membership-access__title-line">{{ $s->text('title_2') }}</span>
            </h2>

            <p class="membership-access__copy">
                {{ $s->text('copy') }}
            </p>

            {{-- One LOT now carries both benefits, so it takes two readings;
                 the third says what a further LOT adds. The row is a wrapping
                 flex line, so a third item needs no layout change. --}}
            <div class="membership-access__rule">
                <div class="membership-access__rule-item">
                    <p class="membership-access__rule-label">{{ $s->text('rule_one_lot') }}</p>
                    <p class="membership-access__rule-value">{{ $membership->usageRightsPerYear() }}<span class="membership-access__times">×</span></p>
                    <p class="membership-access__rule-unit">{{ $s->text('unit_rights') }}</p>
                </div>

                <div class="membership-access__rule-item">
                    <p class="membership-access__rule-label">{{ $s->text('rule_one_lot') }}</p>
                    <p class="membership-access__rule-value">{{ $membership->baseDiscountedRights() }}<span class="membership-access__times">×</span></p>
                    <p class="membership-access__rule-unit">{{ $s->text('unit_discounted') }}</p>
                </div>

                <div class="membership-access__rule-item">
                    <p class="membership-access__rule-label">{{ $s->text('rule_additional') }}</p>
                    <p class="membership-access__rule-value">+{{ $membership->additionalLotRights() }}<span class="membership-access__times">×</span></p>
                    <p class="membership-access__rule-unit">{{ $s->text('unit_discounted') }}</p>
                </div>
            </div>

            <div class="membership-access__examples">
                @foreach ($lotExamples as $example)
                    <div class="membership-access__example">
                        <p class="membership-access__example-lots">{{ $example['lots'] }}</p>

                        <p class="membership-access__example-rights">{{ $example['rights'] }}</p>
                        <p class="membership-access__example-unit">{{ $s->text('unit_rights') }}</p>

                        <p class="membership-access__example-rights membership-access__example-rights--discounted">{{ $example['discounted'] }}</p>
                        <p class="membership-access__example-unit">{{ $s->text('unit_discounted') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- The calculator works out Usage Rights only — there is no additional
             LOT price to calculate with. Its inputs are the business settings,
             handed to the script as data attributes. --}}
        <div
            class="membership-access__calculator"
            data-calculator
            @foreach ($membership->calculatorAttributes() as $attribute => $value) {{ $attribute }}="{{ $value }}" @endforeach
            data-reveal
            data-reveal-delay="1"
        >
            <p class="membership-access__calculator-title">{{ $s->text('calculator_title') }}</p>
            <p class="membership-access__calculator-copy">
                {{ $s->text('calculator_copy') }}
            </p>

            <div class="membership-access__stepper">
                <button
                    type="button"
                    class="membership-access__step"
                    data-calculator-decrease
                    aria-label="{{ $s->text('decrease') }}"
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
                    aria-label="{{ $s->text('increase') }}"
                >+</button>
            </div>

            {{-- Four readings: each benefit per year, then each across the
                 membership. The script replaces the values; these are what a
                 visitor without JavaScript sees for a single LOT. --}}
            <div class="membership-access__results" aria-live="polite">
                <div class="membership-access__result">
                    <p class="membership-access__result-value" data-calculator-annual>{{ $membership->usageRightsPerYear() }}×</p>
                    <p class="membership-access__result-label">{{ $s->text('result_annual') }}</p>
                </div>

                <div class="membership-access__result">
                    <p class="membership-access__result-value" data-calculator-annual-discounted>{{ $membership->discountedRightsFor(1) }}×</p>
                    <p class="membership-access__result-label">{{ $s->text('unit_discounted') }}</p>
                </div>

                <div class="membership-access__result">
                    <p class="membership-access__result-value" data-calculator-total>{{ $membership->totalUsageRights() }}×</p>
                    <p class="membership-access__result-label">{{ $s->text('result_total') }}</p>
                </div>

                <div class="membership-access__result">
                    <p class="membership-access__result-value" data-calculator-total-discounted>{{ $membership->totalDiscountedRightsFor(1) }}×</p>
                    <p class="membership-access__result-label">{{ $s->text('result_total_discounted') }}</p>
                </div>
            </div>

            <p class="membership-access__calculator-note">
                {{ $s->text('calculator_note') }}
            </p>
        </div>
    </div>
</section>
