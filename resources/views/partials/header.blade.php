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
    </div>
</header>
