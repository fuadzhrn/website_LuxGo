@php
    $membershipFaqs = [
        [
            'question' => 'What is a LUX&GO LOT Membership?',
            'answer' => '1 LOT is the base Membership unit that provides access to LUX&GO Usage Rights during the Membership period.',
        ],
        [
            'question' => 'How long is the Membership valid?',
            'answer' => 'The Membership is valid for 5 years.',
        ],
        [
            'question' => 'How many Usage Rights do I receive with 1 LOT?',
            'answer' => '1 LOT provides 6 Usage Rights per year, equal to 30 Usage Rights over 5 years.',
        ],
        [
            'question' => 'What happens when I add more LOTs?',
            'answer' => 'Every additional LOT adds 2 Usage Rights per year.',
        ],
        [
            'question' => 'Is there a fee each time I use the vehicle?',
            'answer' => 'Yes. The Member Usage Fee is Rp750.000 for 12 hours of vehicle use. Professional driver is included.',
        ],
        [
            'question' => 'Is a professional driver included?',
            'answer' => 'Yes. LUX&GO vehicle usage includes a professional driver.',
        ],
        [
            'question' => 'What happens after my Usage Rights are used?',
            'answer' => 'The vehicle can still be used based on availability.',
            'breakdown' => [
                ['label' => 'Regular Usage', 'value' => 'Rp750.000'],
                ['label' => 'Additional Usage', 'value' => '+ Rp500.000'],
                ['label' => 'Total', 'value' => 'Rp1.250.000 / 12 Hours', 'total' => true],
            ],
        ],
    ];
@endphp

<section class="membership-faq">
    <div class="lux-container membership-faq__inner">
        <div class="membership-faq__intro" data-reveal>
            <p class="membership-faq__eyebrow">Membership FAQ</p>

            <h2 class="membership-faq__title">
                <span class="membership-faq__title-line">Everything You</span>
                <span class="membership-faq__title-line">Need to Know.</span>
            </h2>

            <p class="membership-faq__copy">
                The essentials of a LUX&amp;GO Membership — how it works, what you receive, and what applies each time you use the vehicle.
            </p>
        </div>

        <div class="membership-faq__list" data-faq data-reveal data-reveal-delay="1">
            @foreach ($membershipFaqs as $index => $faq)
                @php
                    $number = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                    $questionId = 'faq-question-'.($index + 1);
                    $answerId = 'faq-answer-'.($index + 1);
                @endphp

                <div class="membership-faq__item">
                    <h3 class="membership-faq__heading">
                        <button
                            type="button"
                            class="membership-faq__question"
                            id="{{ $questionId }}"
                            aria-expanded="true"
                            aria-controls="{{ $answerId }}"
                            data-faq-question
                        >
                            <span class="membership-faq__number">{{ $number }}</span>
                            <span class="membership-faq__label">{{ $faq['question'] }}</span>
                            <span class="membership-faq__icon" aria-hidden="true">
                                <img
                                    src="{{ asset('assets/icons/luxgo/membership/faq/plus.svg') }}"
                                    alt=""
                                    class="membership-faq__icon-plus"
                                    width="18"
                                    height="18"
                                    loading="lazy"
                                >
                                <img
                                    src="{{ asset('assets/icons/luxgo/membership/faq/minus.svg') }}"
                                    alt=""
                                    class="membership-faq__icon-minus"
                                    width="18"
                                    height="18"
                                    loading="lazy"
                                >
                            </span>
                        </button>
                    </h3>

                    <div
                        class="membership-faq__answer"
                        id="{{ $answerId }}"
                        role="region"
                        aria-labelledby="{{ $questionId }}"
                        data-faq-answer
                    >
                        <p class="membership-faq__answer-text">{{ $faq['answer'] }}</p>

                        @isset($faq['breakdown'])
                            <div class="membership-faq__breakdown">
                                @foreach ($faq['breakdown'] as $row)
                                    <div class="membership-faq__row{{ ! empty($row['total']) ? ' membership-faq__row--total' : '' }}">
                                        <span class="membership-faq__row-label">{{ $row['label'] }}</span>
                                        <span class="membership-faq__row-value">{{ $row['value'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endisset
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
