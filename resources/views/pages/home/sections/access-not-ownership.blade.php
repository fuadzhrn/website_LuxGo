@php
    /* Five considerations, fixed by the layout — the CMS supplies the wording,
       the icons stay with the design. */
    $ownershipConsiderations = [
        ['label' => $s->text('items.price'), 'icon' => 'tag.svg'],
        ['label' => $s->text('items.depreciation'), 'icon' => 'trending-down.svg'],
        ['label' => $s->text('items.maintenance'), 'icon' => 'wrench.svg'],
        ['label' => $s->text('items.insurance'), 'icon' => 'shield-check.svg'],
        ['label' => $s->text('items.operational'), 'icon' => 'wallet.svg'],
    ];
@endphp

<section class="home-section home-access">
    <div class="lux-container">
        <div class="home-access__grid">
            <div class="home-access__lead" data-reveal>
                <span class="home-access__accent" aria-hidden="true"></span>

                <h2 class="home-access__title">
                    <span class="home-access__title-line">{{ $s->text('title_1') }}</span>
                    <span class="home-access__title-line">{{ $s->text('title_2') }}</span>
                </h2>

                <p class="home-access__copy">
                    {{ $s->text('copy') }}
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
