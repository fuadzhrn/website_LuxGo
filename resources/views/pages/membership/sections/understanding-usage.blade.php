@php
    /* What one use costs, at each of the three rates the programme defines.
       The figures come from the membership settings, so the page never states a
       price of its own — and the member rate is shown next to the public rate it
       is discounted from, rather than on its own. */
    $usageTiers = [
        [
            'label' => $s->text('with_rights'),
            'amount' => $s->text('rights_amount'),
            'caption' => $s->text('caption'),
            'note' => $s->text('driver_included'),
        ],
        [
            'label' => $s->text('with_discount'),
            'amount' => $membership->memberUsageFee(),
            'caption' => $s->text('discount_caption'),
            'note' => $s->text('discount_note'),
            'highlight' => true,
        ],
        [
            'label' => $s->text('public_label'),
            'amount' => $membership->publicUsageFee(),
            'caption' => $s->text('public_caption'),
            'note' => $s->text('availability'),
        ],
    ];
@endphp

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
            @foreach ($usageTiers as $index => $tier)
                @if ($index > 0)
                    <span class="membership-usage__divider" aria-hidden="true"></span>
                @endif

                <div class="membership-usage__case @if ($tier['highlight'] ?? false) membership-usage__case--highlight @endif">
                    <p class="membership-usage__case-label">{{ $tier['label'] }}</p>

                    <p class="membership-usage__amount">{{ $tier['amount'] }}</p>
                    <p class="membership-usage__unit">{{ $s->text('unit') }}</p>
                    <p class="membership-usage__caption">{{ $tier['caption'] }}</p>

                    <p class="membership-usage__note">{{ $tier['note'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
