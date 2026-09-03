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
    ];

    $footerContact = [
        ['icon' => 'phone.svg', 'label' => '0811-1234-1234', 'href' => 'tel:+6281112341234'],
        ['icon' => 'mail.svg', 'label' => 'info@luxandgo.com', 'href' => 'mailto:info@luxandgo.com'],
        ['icon' => 'instagram.svg', 'label' => '@luxandgo', 'href' => 'https://www.instagram.com/luxandgo'],
        ['icon' => 'tiktok.svg', 'label' => '@luxandgo', 'href' => 'https://www.tiktok.com/@luxandgo'],
    ];
@endphp

<footer class="site-footer">
    <div class="lux-container">
        <div class="site-footer__main">
            <div class="site-footer__brand-col">
                <a href="{{ route('home') }}" class="site-footer__brand">LUX&amp;GO</a>
                <p class="site-footer__tagline">Premium Mobility Membership</p>
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

            <div class="site-footer__col">
                <h2 class="site-footer__heading">Contact</h2>
                <ul class="site-footer__list site-footer__list--contact">
                    @foreach ($footerContact as $contact)
                        <li class="site-footer__contact-item">
                            <img
                                src="{{ asset('assets/icons/luxgo/footer/'.$contact['icon']) }}"
                                alt=""
                                class="site-footer__contact-icon"
                                width="16"
                                height="16"
                                loading="lazy"
                            >
                            <a
                                href="{{ $contact['href'] }}"
                                class="site-footer__link"
                                @if (str_starts_with($contact['href'], 'https://')) target="_blank" rel="noopener" @endif
                            >{{ $contact['label'] }}</a>
                        </li>
                    @endforeach
                    <li class="site-footer__location">Jakarta Pusat</li>
                </ul>
            </div>
        </div>

        <div class="site-footer__bottom">
            <p>&copy; {{ date('Y') }} LUX&amp;GO. All rights reserved.</p>
            <p>PT Dwimuria Investama Properti</p>
        </div>
    </div>
</footer>
