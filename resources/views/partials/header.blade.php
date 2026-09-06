@php
    $siteNavItems = [
        ['label' => 'Home', 'href' => route('home'), 'active' => request()->routeIs('home')],
        ['label' => 'Membership', 'href' => '/membership', 'active' => request()->is('membership*')],
        ['label' => 'Our Collection', 'href' => '/collection', 'active' => request()->is('collection*')],
        ['label' => 'Experience', 'href' => '/experience', 'active' => request()->is('experience*')],
        ['label' => 'How It Works', 'href' => '/how-it-works', 'active' => request()->is('how-it-works*')],
        ['label' => 'About', 'href' => '/about', 'active' => request()->is('about*')],
    ];
@endphp

<header class="site-header" data-site-header>
    <div class="lux-container site-header__inner">
        <a href="{{ route('home') }}" class="site-header__brand" aria-label="LUX&GO — Home">
            LUX&amp;GO
        </a>

        <nav class="site-nav" aria-label="Primary navigation">
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

        <a href="/become-a-member" class="site-header__cta">Become a Member</a>

        {{-- Replaces the desktop nav and CTA below 1024px; behaviour lives in header.js. --}}
        <button
            type="button"
            class="site-header__toggle"
            data-menu-toggle
            aria-label="Open menu"
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
    <nav class="lux-container site-menu__inner" aria-label="Mobile navigation">
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
            <span>Become a Member</span>
            <span class="site-menu__cta-icon" aria-hidden="true">&rarr;</span>
        </a>
    </nav>
</div>
