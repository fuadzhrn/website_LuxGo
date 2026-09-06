@php
    $locale = app()->getLocale();
    $fallbackLocale = 'en';

    /* Active questions, in the order the CMS holds them. A question with no
       translation in this locale falls back to English rather than showing
       nothing. */
    $membershipFaqs = $s->faqItems()->map(function ($item) use ($s, $locale, $fallbackLocale) {
        $translation = $item->translation($locale) ?? $item->translation($fallbackLocale);

        return [
            'question' => $s->substitute($translation?->question),
            'answer' => $s->substitute($translation?->answer),
            'breakdown' => $item->shows_usage_breakdown,
        ];
    })->filter(fn (array $faq) => $faq['question'] !== '')->values();
@endphp

<section class="membership-faq">
    <div class="lux-container membership-faq__inner">
        <div class="membership-faq__intro" data-reveal>
            <p class="membership-faq__eyebrow">{{ $s->text('eyebrow') }}</p>

            <h2 class="membership-faq__title">
                <span class="membership-faq__title-line">{{ $s->text('title_1') }}</span>
                <span class="membership-faq__title-line">{{ $s->text('title_2') }}</span>
            </h2>

            <p class="membership-faq__copy">
                {{ $s->text('copy') }}
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

                        {{-- The amounts in the breakdown come from the
                             membership settings, so this table can never
                             disagree with the section above it. --}}
                        @if ($faq['breakdown'])
                            <div class="membership-faq__breakdown">
                                <div class="membership-faq__row">
                                    <span class="membership-faq__row-label">{{ $s->text('row_regular') }}</span>
                                    <span class="membership-faq__row-value">{{ $membership->memberUsageFee() }}</span>
                                </div>

                                <div class="membership-faq__row">
                                    <span class="membership-faq__row-label">{{ $s->text('row_additional') }}</span>
                                    <span class="membership-faq__row-value">+ {{ $membership->additionalUsageFee() }}</span>
                                </div>

                                <div class="membership-faq__row membership-faq__row--total">
                                    <span class="membership-faq__row-label">{{ $s->text('row_total') }}</span>
                                    <span class="membership-faq__row-value">{{ $s->text('row_total_value') }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
