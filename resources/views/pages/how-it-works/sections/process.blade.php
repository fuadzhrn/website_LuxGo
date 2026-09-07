@php
    /* Three steps, fixed by the layout: JOIN, BOOK, USE. The CMS supplies their
       wording; the numbering and icons stay with the design. */
    $processFlow = [
        ['number' => '01', 'icon' => 'user-round.svg', 'title' => $s->text('steps.join.title'), 'copy' => $s->text('steps.join.copy')],
        ['number' => '02', 'icon' => 'calendar.svg', 'title' => $s->text('steps.book.title'), 'copy' => $s->text('steps.book.copy')],
        ['number' => '03', 'icon' => 'car-front.svg', 'title' => $s->text('steps.use.title'), 'copy' => $s->text('steps.use.copy')],
    ];
@endphp

<section class="hiw-section hiw-process" id="the-process">
    <div class="lux-container">
        <div class="hiw-process__header" data-reveal>
            <div class="hiw-process__heading">
                <p class="hiw-process__eyebrow">{{ $s->text('eyebrow') }}</p>
                <h2 class="hiw-process__title">{{ $s->text('title') }}</h2>
            </div>

            <p class="hiw-process__intro">
                {{ $s->text('intro') }}
            </p>
        </div>

        {{-- One horizontal flow, connected by a hairline — never three cards. --}}
        <ol class="hiw-process__flow" data-reveal data-reveal-delay="1">
            @foreach ($processFlow as $step)
                <li class="hiw-process__step">
                    <p class="hiw-process__number">{{ $step['number'] }}</p>

                    <div class="hiw-process__track">
                        <span class="hiw-process__marker">
                            <img
                                src="{{ asset('assets/icons/luxgo/process/'.$step['icon']) }}"
                                alt=""
                                class="hiw-process__icon"
                                width="20"
                                height="20"
                                loading="lazy"
                            >
                        </span>

                        @unless ($loop->last)
                            <span class="hiw-process__connector" aria-hidden="true"></span>
                        @endunless
                    </div>

                    <h3 class="hiw-process__step-title">{{ $step['title'] }}</h3>
                    <p class="hiw-process__step-copy">{{ $step['copy'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>
