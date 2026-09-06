<section class="home-hero">
    <div class="home-hero__media" aria-hidden="true">
        <img
            src="{{ asset('assets/images/luxgo/home/hero/gambar_bg.png') }}"
            alt=""
            class="home-hero__image"
            width="1783"
            height="882"
            loading="eager"
            fetchpriority="high"
        >
    </div>

    <div class="home-hero__overlay" aria-hidden="true"></div>

    <div class="lux-container home-hero__container">
        <div class="home-hero__content">
            <h1 class="home-hero__title">
                <span class="home-hero__title-line">{{ __('home.hero.title_1') }}</span>
                <span class="home-hero__title-line">{{ __('home.hero.title_2') }}</span>
                <span class="home-hero__title-line">{{ __('home.hero.title_3') }}</span>
                <span class="home-hero__title-line">{{ __('home.hero.title_4') }}</span>
            </h1>

            <span class="home-hero__accent" aria-hidden="true"></span>

            <p class="home-hero__description">
                {{ __('home.hero.description') }}
            </p>

            <div class="home-hero__actions">
                <a href="{{ route('membership') }}" class="home-hero__cta">
                    <span>{{ __('home.hero.cta') }}</span>
                    <span class="home-hero__cta-icon" aria-hidden="true">&rarr;</span>
                </a>
            </div>
        </div>
    </div>
</section>
