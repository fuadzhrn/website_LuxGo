<section class="hiw-section hiw-closing">
    <div class="lux-container hiw-closing__inner" data-reveal>
        <div class="hiw-closing__content">
            <h2 class="hiw-closing__title">
                <span class="hiw-closing__title-line">{{ $s->text('title_1') }}</span>
                <span class="hiw-closing__title-line">{{ $s->text('title_2') }}</span>
            </h2>

            <p class="hiw-closing__copy">
                {{ $s->text('copy') }}
            </p>
        </div>

        <a href="{{ $s->link('cta_target') }}" class="hiw-closing__link">
            <span>{{ __('global.cta.become_member') }}</span>
            <span class="hiw-closing__link-icon" aria-hidden="true">&rarr;</span>
        </a>
    </div>
</section>
