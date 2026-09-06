@php
    /* route() carries the active locale automatically — SetLocale registers it as
       a URL default — so these links never drop the visitor into another language. */
    $siteNavItems = [
        ['label' => __('global.nav.home'), 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => __('global.nav.membership'), 'href' => route('membership'), 'active' => request()->routeIs('membership')],
        ['label' => __('global.nav.collection'), 'href' => route('collection'), 'active' => request()->routeIs('collection')],
        ['label' => __('global.nav.experience'), 'href' => route('experience'), 'active' => request()->routeIs('experience')],
        ['label' => __('global.nav.how_it_works'), 'href' => route('how-it-works'), 'active' => request()->routeIs('how-it-works')],
        ['label' => __('global.nav.about'), 'href' => route('about'), 'active' => request()->routeIs('about')],
    ];
@endphp

<header class="site-header" data-site-header>
    <div class="lux-container site-header__inner">
        <a href="{{ route('home') }}" class="site-header__brand" aria-label="{{ __('global.header.brand_aria') }}">
            LUX&amp;GO
        </a>

        <nav class="site-nav" aria-label="{{ __('global.header.primary_nav') }}">
            <ul class="site-nav__list">
                @foreach ($siteNavItems as $item)
                    <li class="site-nav__item">
                        <a
                            href="{{ $item['href'] }}"
                            class="site-nav__link{{ $item['active'] ? ' is-active' : '' }}"
                            @if ($item['active']) aria-current="page" @endif
                        >{{ $item['label'] }}</a>
                    </li>
                @endforeach
            </ul>
        </nav>

        {{-- Grouped with the CTA so the bar keeps three flex children and the
             navigation stays where it was. --}}
        <div class="site-header__actions">
            <div class="site-header__lang" role="group" aria-label="{{ __('ui.language') }}">
                @foreach ($localeAlternates as $code => $alternate)
                    <a
                        href="{{ $alternate['url'] }}"
                        class="site-header__lang-link{{ $alternate['active'] ? ' is-active' : '' }}"
                        hreflang="{{ $code }}"
                        @if ($alternate['active']) aria-current="true" @endif
                    >{{ strtoupper($code) }}</a>
                @endforeach
            </div>

            <a href="/become-a-member" class="site-header__cta">{{ __('global.cta.become_member') }}</a>
        </div>

        {{-- Replaces the desktop nav and CTA below 1024px; behaviour lives in header.js. --}}
        <button
            type="button"
            class="site-header__toggle"
            data-menu-toggle
            aria-label="{{ __('global.header.menu_open') }}"
            data-label-open="{{ __('global.header.menu_open') }}"
            data-label-close="{{ __('global.header.menu_close') }}"
            aria-expanded="false"
            aria-controls="site-menu"
        >
            <img
                src="{{ asset('assets/icons/luxgo/ui/menu.svg') }}"
                alt=""
                class="site-header__toggle-icon"
                data-menu-icon="open"
                width="24"
                height="24"
            >
            <img
                src="{{ asset('assets/icons/luxgo/ui/x.svg') }}"
                alt=""
                class="site-header__toggle-icon"
                data-menu-icon="close"
                width="24"
                height="24"
                hidden
            >
        </button>
    </div>
</header>

{{-- Sits behind the header bar, so the open state reads as "LUX&GO … ×" over a
     full-screen dark panel. Never rendered above 1024px. --}}
<div class="site-menu" id="site-menu" data-site-menu>
    <nav class="lux-container site-menu__inner" aria-label="{{ __('global.header.mobile_nav') }}">
        <ul class="site-menu__list">
            @foreach ($siteNavItems as $item)
                <li class="site-menu__item">
                    <a
                        href="{{ $item['href'] }}"
                        class="site-menu__link{{ $item['active'] ? ' is-active' : '' }}"
                        @if ($item['active']) aria-current="page" @endif
                    >{{ $item['label'] }}</a>
                </li>
            @endforeach
        </ul>

        <a href="/become-a-member" class="site-menu__cta">
            <span>{{ __('global.cta.become_member') }}</span>
            <span class="site-menu__cta-icon" aria-hidden="true">&rarr;</span>
        </a>

        <div class="site-menu__lang" role="group" aria-label="{{ __('ui.language') }}">
            <p class="site-menu__lang-label">{{ __('ui.language') }}</p>

            <div class="site-menu__lang-links">
                @foreach ($localeAlternates as $code => $alternate)
                    <a
                        href="{{ $alternate['url'] }}"
                        class="site-menu__lang-link{{ $alternate['active'] ? ' is-active' : '' }}"
                        hreflang="{{ $code }}"
                        @if ($alternate['active']) aria-current="true" @endif
                    >{{ strtoupper($code) }}</a>
                @endforeach
            </div>
        </div>
    </nav>
</div>
