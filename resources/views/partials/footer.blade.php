@php
    $footerColumns = [
        [
            'id' => 'footer-membership',
            'heading' => 'Membership',
            'links' => [
                ['label' => 'Membership', 'href' => '/membership'],
                ['label' => 'Become a Member', 'href' => '/become-a-member'],
            ],
        ],
        [
            'id' => 'footer-collection',
            'heading' => 'Our Collection',
            'links' => [
                ['label' => 'Our Collection', 'href' => '/collection'],
                ['label' => 'Experience', 'href' => '/experience'],
            ],
        ],
        [
            'id' => 'footer-company',
            'heading' => 'Company',
            'links' => [
                ['label' => 'About', 'href' => '/about'],
                ['label' => 'How It Works', 'href' => '/how-it-works'],
            ],
        ],
        [
            'id' => 'footer-legal',
            'heading' => 'Legal',
            'links' => [
                ['label' => 'Terms of Use', 'href' => route('legal.terms')],
                ['label' => 'Privacy Policy', 'href' => route('legal.privacy')],
                ['label' => 'Cookies Policy', 'href' => route('legal.cookies')],
            ],
        ],
    ];

    $footerSocials = [
        ['icon' => 'instagram.svg', 'label' => 'Instagram', 'href' => 'https://www.instagram.com/luxandgo'],
        ['icon' => 'tiktok.svg', 'label' => 'TikTok', 'href' => 'https://www.tiktok.com/@luxandgo'],
    ];

    $footerContact = [
        ['label' => '0811-1234-1234', 'href' => 'tel:+6281112341234'],
        ['label' => 'info@luxandgo.com', 'href' => 'mailto:info@luxandgo.com'],
        ['label' => 'Jakarta Pusat', 'href' => null],
    ];
@endphp

<footer class="site-footer">
    <div class="lux-container">
        <div class="site-footer__main">
            <div class="site-footer__brand-col">
                <a href="{{ route('home') }}" class="site-footer__brand">LUX&amp;GO</a>

                <p class="site-footer__tagline">
                    <span class="site-footer__tagline-line">Premium mobility.</span>
                    <span class="site-footer__tagline-line">Without ownership.</span>
                </p>

                <ul class="site-footer__socials">
                    @foreach ($footerSocials as $social)
                        <li>
                            <a
                                href="{{ $social['href'] }}"
                                class="site-footer__social"
                                target="_blank"
                                rel="noopener"
                                aria-label="{{ $social['label'] }}"
                            >
                                <img
                                    src="{{ asset('assets/icons/luxgo/footer/'.$social['icon']) }}"
                                    alt=""
                                    width="16"
                                    height="16"
                                    loading="lazy"
                                >
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>

            @foreach ($footerColumns as $column)
                <nav class="site-footer__col" aria-labelledby="{{ $column['id'] }}">
                    <h2 class="site-footer__heading" id="{{ $column['id'] }}">{{ $column['heading'] }}</h2>
                    <ul class="site-footer__list">
                        @foreach ($column['links'] as $link)
                            <li>
                                <a href="{{ $link['href'] }}" class="site-footer__link">{{ $link['label'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
            @endforeach

            <span class="site-footer__divider" aria-hidden="true"></span>

            <div class="site-footer__col">
                <h2 class="site-footer__heading">Contact</h2>
                <ul class="site-footer__list">
                    @foreach ($footerContact as $contact)
                        <li>
                            @if ($contact['href'])
                                <a href="{{ $contact['href'] }}" class="site-footer__link">{{ $contact['label'] }}</a>
                            @else
                                <span class="site-footer__link">{{ $contact['label'] }}</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div class="site-footer__bottom">
            <p>&copy; {{ date('Y') }} LUX&amp;GO. All rights reserved.</p>
            <p>PT Dwimuria Investama Properti</p>
        </div>
    </div>
</footer>
