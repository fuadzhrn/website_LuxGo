<section class="membership-cta">
    <div class="lux-container membership-cta__inner" data-reveal>
        <div class="membership-cta__content">
            <p class="membership-cta__kicker">{{ $s->text('cta.kicker') }}</p>

            <h2 class="membership-cta__title">
                <span class="membership-cta__title-line">{{ $s->text('cta.title_1') }}</span>
                <span class="membership-cta__title-line">{{ $s->text('cta.title_2') }}</span>
            </h2>

            <p class="membership-cta__copy">
                {{ $s->text('cta.copy') }}
            </p>
        </div>

        <a href="{{ $s->link('cta_target') }}" class="membership-cta__link">
            <span>{{ __('global.cta.become_member') }}</span>
            <span class="membership-cta__link-icon" aria-hidden="true">&rarr;</span>
        </a>
    </div>
</section>
