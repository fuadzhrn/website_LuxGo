@php
    $lotExamples = [
        ['lots' => '1 LOT', 'rights' => '6×'],
        ['lots' => '5 LOT', 'rights' => '14×'],
        ['lots' => '10 LOT', 'rights' => '24×'],
    ];
@endphp

<section class="membership-access">
    <div class="lux-container membership-access__inner">
        <div class="membership-access__intro" data-reveal>
            <p class="membership-access__eyebrow">{{ __('membership.access.eyebrow') }}</p>

            <h2 class="membership-access__title">
                <span class="membership-access__title-line">{{ __('membership.access.title_1') }}</span>
                <span class="membership-access__title-line">{{ __('membership.access.title_2') }}</span>
            </h2>

            <p class="membership-access__copy">
                {{ __('membership.access.copy') }}
            </p>

            <div class="membership-access__rule">
                <div class="membership-access__rule-item">
                    <p class="membership-access__rule-label">{{ __('membership.access.rule_one_lot') }}</p>
                    <p class="membership-access__rule-value">6<span class="membership-access__times">×</span></p>
                    <p class="membership-access__rule-unit">{{ __('membership.access.unit_per_year') }}</p>
                </div>

                <div class="membership-access__rule-item">
                    <p class="membership-access__rule-label">{{ __('membership.access.rule_additional') }}</p>
                    <p class="membership-access__rule-value">+2<span class="membership-access__times">×</span></p>
                    <p class="membership-access__rule-unit">{{ __('membership.access.unit_per_year') }}</p>
                </div>
            </div>

            <div class="membership-access__examples">
                @foreach ($lotExamples as $example)
                    <div class="membership-access__example">
                        <p class="membership-access__example-lots">{{ $example['lots'] }}</p>
                        <p class="membership-access__example-rights">{{ $example['rights'] }}</p>
                        <p class="membership-access__example-unit">{{ __('membership.access.unit_per_year') }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="membership-access__calculator" data-calculator data-reveal data-reveal-delay="1">
            <p class="membership-access__calculator-title">{{ __('membership.access.calculator_title') }}</p>
            <p class="membership-access__calculator-copy">
                {{ __('membership.access.calculator_copy') }}
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
                    <p class="membership-access__result-label">{{ __('membership.access.result_annual') }}</p>
                </div>

                <div class="membership-access__result">
                    <p class="membership-access__result-value" data-calculator-total>30×</p>
                    <p class="membership-access__result-label">{{ __('membership.access.result_total') }}</p>
                </div>
            </div>

            <p class="membership-access__calculator-note">
                {{ __('membership.access.calculator_note') }}
            </p>
        </div>
    </div>
</section>
