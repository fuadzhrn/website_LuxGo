@php
    $audiences = [
        ['number' => '01', 'label' => 'Business Owners'],
        ['number' => '02', 'label' => 'Premium Executives'],
        ['number' => '03', 'label' => 'Affluent Families'],
        ['number' => '04', 'label' => 'Professionals'],
        ['number' => '05', 'label' => 'Corporate'],
    ];
@endphp

<section class="about-section about-intro">
    <div class="lux-container about-intro__inner">
        <div class="about-intro__lead">
            <p class="about-intro__eyebrow" data-enter>About LUX&amp;GO</p>

            <h1 class="about-intro__title" data-enter data-enter-delay="1">
                <span class="about-intro__title-line">Premium Mobility,</span>
                <span class="about-intro__title-line">Reimagined.</span>
            </h1>

            <p class="about-intro__copy" data-enter data-enter-delay="2">
                LUX&amp;GO is a premium mobility membership built to give flexible access to premium vehicles,
                without the burden of ownership.
            </p>
        </div>

        <div class="about-intro__serve" data-enter data-enter-delay="3">
            <h2 class="about-intro__serve-title">Who We Serve</h2>

            {{-- Five audiences as a numbered editorial register — never five cards. --}}
            <ol class="about-intro__list">
                @foreach ($audiences as $audience)
                    <li class="about-intro__item">
                        <span class="about-intro__number">{{ $audience['number'] }}</span>
                        <span class="about-intro__label">{{ $audience['label'] }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>
