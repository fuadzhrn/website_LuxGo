@php
    $processFlow = [
        [
            'number' => '01',
            'icon' => 'user-round.svg',
            'title' => 'Join',
            'copy' => 'Become a LUX&GO Member through LOT Membership.',
        ],
        [
            'number' => '02',
            'icon' => 'calendar.svg',
            'title' => 'Book',
            'copy' => 'Reserve the vehicle based on availability.',
        ],
        [
            'number' => '03',
            'icon' => 'car-front.svg',
            'title' => 'Use',
            'copy' => 'Enjoy the vehicle for 12 hours with a professional driver.',
        ],
    ];
@endphp

<section class="hiw-section hiw-process" id="the-process">
    <div class="lux-container">
        <div class="hiw-process__header" data-reveal>
            <div class="hiw-process__heading">
                <p class="hiw-process__eyebrow">The Process</p>
                <h2 class="hiw-process__title">Join. Book. Use.</h2>
            </div>

            <p class="hiw-process__intro">
                Three steps stand between a LUX&amp;GO membership and the moment you step into the vehicle.
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
