@php
    $headOffice = [
        'Gajah Mada Tower, Lt. 19-01',
        'Jl. Gajah Mada No.19-26',
        'Petojo Utara, Gambir',
        'Jakarta Pusat 10130',
    ];

    /* Icons and destinations are the ones already carried by the approved footer —
       no new assets, and no URL invented for this section. */
    $contactChannels = [
        ['icon' => 'phone.svg', 'label' => __('about.contact.label_whatsapp'), 'value' => '0811-1234-1234', 'href' => 'tel:+6281112341234', 'external' => false],
        ['icon' => 'mail.svg', 'label' => __('about.contact.label_email'), 'value' => 'info@luxandgo.com', 'href' => 'mailto:info@luxandgo.com', 'external' => false],
        ['icon' => 'instagram.svg', 'label' => __('about.contact.label_instagram'), 'value' => '@luxandgo', 'href' => 'https://www.instagram.com/luxandgo', 'external' => true],
        ['icon' => 'tiktok.svg', 'label' => __('about.contact.label_tiktok'), 'value' => '@luxandgo', 'href' => 'https://www.tiktok.com/@luxandgo', 'external' => true],
    ];
@endphp

<section class="about-section about-contact" id="contact">
    <div class="lux-container about-contact__inner">
        <div class="about-contact__identity" data-reveal>
            <p class="about-contact__eyebrow">{{ __('about.contact.eyebrow') }}</p>

            <h2 class="about-contact__title">
                <span class="about-contact__title-line">{{ __('about.contact.title_1') }}</span>
                <span class="about-contact__title-line">{{ __('about.contact.title_2') }}</span>
            </h2>

            <p class="about-contact__company">PT Dwimuria Investama Properti</p>

            <address class="about-contact__address">
                @foreach ($headOffice as $line)
                    <span class="about-contact__address-line">{{ $line }}</span>
                @endforeach
            </address>
        </div>

        <div class="about-contact__channels" data-reveal data-reveal-delay="1">
            <h3 class="about-contact__channels-title">{{ __('about.contact.channels_title') }}</h3>

            {{-- Hairline-separated rows, each a single link — never four cards. --}}
            <ul class="about-contact__list">
                @foreach ($contactChannels as $channel)
                    <li class="about-contact__row">
                        <a
                            href="{{ $channel['href'] }}"
                            class="about-contact__link"
                            @if ($channel['external']) target="_blank" rel="noopener" @endif
                        >
                            <img
                                src="{{ asset('assets/icons/luxgo/footer/'.$channel['icon']) }}"
                                alt=""
                                class="about-contact__icon"
                                width="16"
                                height="16"
                                loading="lazy"
                            >

                            <span class="about-contact__channel">
                                <span class="about-contact__label">{{ $channel['label'] }}</span>
                                <span class="about-contact__value">{{ $channel['value'] }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</section>
