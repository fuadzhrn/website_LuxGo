@php
    $membershipFaqs = [
        [
            'question' => __('membership.faq.q1'),
            'answer' => __('membership.faq.a1'),
        ],
        [
            'question' => __('membership.faq.q2'),
            'answer' => __('membership.faq.a2'),
        ],
        [
            'question' => __('membership.faq.q3'),
            'answer' => __('membership.faq.a3'),
        ],
        [
            'question' => __('membership.faq.q4'),
            'answer' => __('membership.faq.a4'),
        ],
        [
            'question' => __('membership.faq.q5'),
            'answer' => __('membership.faq.a5'),
        ],
        [
            'question' => __('membership.faq.q6'),
            'answer' => __('membership.faq.a6'),
        ],
        [
            'question' => __('membership.faq.q7'),
            'answer' => __('membership.faq.a7'),
            'breakdown' => [
                ['label' => __('membership.faq.row_regular'), 'value' => 'Rp750.000'],
                ['label' => __('membership.faq.row_additional'), 'value' => '+ Rp500.000'],
                ['label' => __('membership.faq.row_total'), 'value' => __('membership.faq.row_total_value'), 'total' => true],
            ],
        ],
    ];
@endphp

<section class="membership-faq">
    <div class="lux-container membership-faq__inner">
        <div class="membership-faq__intro" data-reveal>
            <p class="membership-faq__eyebrow">{{ __('membership.faq.eyebrow') }}</p>

            <h2 class="membership-faq__title">
                <span class="membership-faq__title-line">{{ __('membership.faq.title_1') }}</span>
                <span class="membership-faq__title-line">{{ __('membership.faq.title_2') }}</span>
            </h2>

            <p class="membership-faq__copy">
                {{ __('membership.faq.copy') }}
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
