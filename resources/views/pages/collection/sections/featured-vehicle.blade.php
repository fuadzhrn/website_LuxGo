@php
    $featuredImage = 'assets/images/luxgo/collection/featured/denza-d9-main.webp';

    $vehicleFeatures = [
        ['icon' => 'gem.svg', 'label' => 'Premium Design'],
        ['icon' => 'armchair.svg', 'label' => 'Luxury Comfort'],
        ['icon' => 'zap.svg', 'label' => 'EV Technology'],
        ['icon' => 'user-round.svg', 'label' => 'Executive Image'],
    ];
@endphp

{{-- Replace with denza-d9-main.webp in public/assets/images/luxgo/collection/featured/.
     Until the file exists the showcase keeps its neutral media slot. --}}

<section class="collection-section collection-featured" id="featured-vehicle">
    <div class="lux-container">
        <div class="collection-featured__header" data-reveal>
            <div class="collection-featured__heading">
                <p class="collection-featured__eyebrow">Featured Vehicle</p>

                <h2 class="collection-featured__title">
                    <span class="collection-featured__title-line">Denza</span>
                    <span class="collection-featured__title-line">D9</span>
                </h2>
            </div>

            <p class="collection-featured__copy">
                Crafted for premium mobility with refined comfort, modern electric character, and executive presence.
            </p>
        </div>

        <div class="collection-featured__showcase" data-reveal data-reveal-delay="1">
            <div class="collection-featured__media">
                @if (file_exists(public_path($featuredImage)))
                    <img
                        src="{{ asset($featuredImage) }}"
                        alt="Denza D9, the featured vehicle of the LUX&GO collection."
                        class="collection-featured__image"
                        loading="lazy"
                    >
                @endif
            </div>

            <div class="collection-featured__rail">
                <ul class="collection-featured__features">
                    @foreach ($vehicleFeatures as $feature)
                        <li class="collection-featured__feature">
                            <img
                                src="{{ asset('assets/icons/luxgo/collection/'.$feature['icon']) }}"
                                alt=""
                                class="collection-featured__feature-icon"
                                width="20"
                                height="20"
                                loading="lazy"
                            >
                            <span class="collection-featured__feature-label">{{ $feature['label'] }}</span>
                        </li>
                    @endforeach
                </ul>

                <a href="{{ route('experience') }}" class="collection-featured__link">
                    <span>Discover the Experience</span>
                    <span class="collection-featured__link-icon" aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</section>
