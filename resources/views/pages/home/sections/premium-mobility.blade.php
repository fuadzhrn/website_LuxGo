@php
    $previewFeatures = [
        ['icon' => 'gem.svg', 'lines' => [__('home.mobility.features.design.line_1'), __('home.mobility.features.design.line_2')]],
        ['icon' => 'armchair.svg', 'lines' => [__('home.mobility.features.comfort.line_1'), __('home.mobility.features.comfort.line_2')]],
        ['icon' => 'zap.svg', 'lines' => [__('home.mobility.features.ev.line_1'), __('home.mobility.features.ev.line_2')]],
        ['icon' => 'user-round.svg', 'lines' => [__('home.mobility.features.executive.line_1'), __('home.mobility.features.executive.line_2')]],
    ];
@endphp

<section class="home-section home-mobility">
    <div class="lux-container home-mobility__container">
        {{-- Kept first so the absolutely positioned frame paints beneath the copy
             on desktop; on mobile it is reordered into the flow between the
             description and the feature grid. --}}
        <div class="home-mobility__media">
            <img
                src="{{ asset('assets/images/luxgo/collection/denza-d9/gambar_bg2.png') }}"
                alt="{{ __('home.mobility.vehicle_alt') }}"
                class="home-mobility__vehicle"
                width="1672"
                height="941"
                loading="lazy"
            >
        </div>

        <div class="home-mobility__content">
            <p class="home-mobility__eyebrow" data-reveal>{{ __('home.mobility.eyebrow') }}</p>

            <h2 class="home-mobility__title" data-reveal data-reveal-delay="1">
                <span class="home-mobility__title-line">{{ __('home.mobility.title_1') }}</span>
                <span class="home-mobility__title-line">{{ __('home.mobility.title_2') }}</span>
            </h2>

            <p class="home-mobility__description" data-reveal data-reveal-delay="2">
                {{ __('home.mobility.description') }}
            </p>
        </div>

        <div class="home-mobility__details">
            <ul class="home-mobility__features" data-reveal data-reveal-delay="3">
                @foreach ($previewFeatures as $feature)
                    <li class="home-mobility__feature">
                        <img
                            src="{{ asset('assets/icons/luxgo/preview/'.$feature['icon']) }}"
                            alt=""
                            class="home-mobility__feature-icon"
                            width="24"
                            height="24"
                            loading="lazy"
                        >
                        <span class="home-mobility__feature-label">
                            @foreach ($feature['lines'] as $line)
                                <span class="home-mobility__feature-line">{{ $line }}</span>
                            @endforeach
                        </span>
                    </li>
                @endforeach
            </ul>

            <div class="home-mobility__driver" data-reveal data-reveal-delay="4">
                <img
                    src="{{ asset('assets/icons/luxgo/preview/user-round-check.svg') }}"
                    alt=""
                    class="home-mobility__driver-icon"
                    width="24"
                    height="24"
                    loading="lazy"
                >
                <div>
                    <p class="home-mobility__driver-title">
                        {{ __('home.mobility.driver_title') }} <span class="home-mobility__driver-accent">{{ __('home.mobility.driver_accent') }}</span>
                    </p>
                    <p class="home-mobility__driver-copy">{{ __('home.mobility.driver_copy') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
