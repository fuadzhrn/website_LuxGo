@php
    $ownershipConsiderations = [
        ['label' => 'High Purchase Price', 'icon' => 'tag.svg'],
        ['label' => 'Depreciation', 'icon' => 'trending-down.svg'],
        ['label' => 'Maintenance', 'icon' => 'wrench.svg'],
        ['label' => 'Insurance', 'icon' => 'shield-check.svg'],
        ['label' => 'Operational Cost', 'icon' => 'wallet.svg'],
    ];
@endphp

<section class="home-section home-access">
    <div class="lux-container">
        <div class="home-access__grid">
            <div class="home-access__lead" data-reveal>
                <span class="home-access__accent" aria-hidden="true"></span>

                <h2 class="home-access__title">
                    <span class="home-access__title-line">Access,</span>
                    <span class="home-access__title-line">Not Ownership.</span>
                </h2>

                <p class="home-access__copy">
                    Not everyone needs to own a premium car. For many customers, premium mobility is needed only at certain moments.
                </p>
            </div>

            <span class="home-access__divider" aria-hidden="true"></span>

            <ul class="home-access__list">
                @foreach ($ownershipConsiderations as $index => $consideration)
                    <li class="home-access__item" data-reveal data-reveal-delay="{{ $index + 1 }}">
                        <img
                            src="{{ asset('assets/icons/luxgo/access/'.$consideration['icon']) }}"
                            alt=""
                            class="home-access__icon"
                            width="24"
                            height="24"
                            loading="lazy"
                        >
                        <span class="home-access__label">{{ $consideration['label'] }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
