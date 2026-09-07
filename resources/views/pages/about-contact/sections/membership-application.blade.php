@php
    /* The fields the approved form has. Their labels come from the CMS; the
       field names, types and behaviour are part of the page. */
    $applicationFields = [
        ['id' => 'apply-name', 'name' => 'full_name', 'label' => $s->text('field_name'), 'type' => 'text', 'autocomplete' => 'name', 'required' => true],
        ['id' => 'apply-phone', 'name' => 'phone', 'label' => $s->text('field_phone'), 'type' => 'tel', 'autocomplete' => 'tel', 'required' => true],
        ['id' => 'apply-email', 'name' => 'email', 'label' => $s->text('field_email'), 'type' => 'email', 'autocomplete' => 'email', 'required' => true],
        ['id' => 'apply-lots', 'name' => 'lots', 'label' => $s->text('field_lots'), 'type' => 'number', 'autocomplete' => 'off', 'required' => false],
    ];
@endphp

<section class="about-section about-apply" id="membership-application">
    <div class="lux-container about-apply__inner">
        <div class="about-apply__lead" data-reveal>
            <p class="about-apply__eyebrow">{{ $s->text('eyebrow') }}</p>

            <h2 class="about-apply__title">
                <span class="about-apply__title-line">{{ $s->text('title_1') }}</span>
                <span class="about-apply__title-line">{{ $s->text('title_2') }}</span>
            </h2>

            <p class="about-apply__copy">
                {{ $s->text('copy') }}
            </p>
        </div>

        {{-- Front-end only for now: validation runs in membership-application.js and
             submission stays blocked. The endpoint is wired up in the next stage. --}}
        <form
            class="about-apply__form"
            data-membership-application
            novalidate
            data-reveal
            data-reveal-delay="1"
            data-error-name="{{ $s->text('error_name') }}"
            data-error-phone="{{ $s->text('error_phone') }}"
            data-error-email-required="{{ $s->text('error_email_required') }}"
            data-error-email-invalid="{{ $s->text('error_email_invalid') }}"
            data-error-lots="{{ $s->text('error_lots') }}"
            data-status-unavailable="{{ $s->text('status_unavailable') }}"
        >
            @foreach ($applicationFields as $field)
                <div class="lux-field">
                    <label class="lux-label" for="{{ $field['id'] }}">{{ $field['label'] }}</label>

                    <input
                        class="about-apply__input"
                        type="{{ $field['type'] }}"
                        id="{{ $field['id'] }}"
                        name="{{ $field['name'] }}"
                        autocomplete="{{ $field['autocomplete'] }}"
                        @if ($field['required']) required @endif
                        @if ($field['type'] === 'number') min="1" step="1" value="1" inputmode="numeric" @endif
                        aria-describedby="{{ $field['id'] }}-error"
                    >

                    <p class="about-apply__error" id="{{ $field['id'] }}-error" data-apply-error hidden></p>
                </div>
            @endforeach

            <div class="lux-field">
                <label class="lux-label" for="apply-message">{{ $s->text('field_message') }} <span class="about-apply__optional">{{ $s->text('optional') }}</span></label>

                <textarea
                    class="about-apply__input about-apply__textarea"
                    id="apply-message"
                    name="message"
                    rows="3"
                ></textarea>
            </div>

            <div class="about-apply__actions">
                <button type="submit" class="about-apply__submit">
                    <span>{{ $s->text('submit') }}</span>
                    <span class="about-apply__submit-icon" aria-hidden="true">&rarr;</span>
                </button>

                <p class="about-apply__status" data-apply-status role="status" aria-live="polite"></p>
            </div>
        </form>
    </div>
</section>
