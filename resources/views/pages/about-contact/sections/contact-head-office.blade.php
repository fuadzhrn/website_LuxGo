@php
    /* Labels come from the CMS; the company details come from the site
       settings, which is the one place they are stored. A channel without a
       destination is not rendered — no address is invented here. */
    $contactChannels = collect([
        ['icon' => 'phone.svg', 'label' => $s->text('label_whatsapp'), 'value' => $site->phone(), 'href' => $site->phoneLink(), 'external' => false],
        ['icon' => 'mail.svg', 'label' => $s->text('label_email'), 'value' => $site->email(), 'href' => $site->emailLink(), 'external' => false],
        ['icon' => 'instagram.svg', 'label' => $s->text('label_instagram'), 'value' => $site->instagramHandle(), 'href' => $site->instagramUrl(), 'external' => true],
        ['icon' => 'tiktok.svg', 'label' => $s->text('label_tiktok'), 'value' => $site->tiktokHandle(), 'href' => $site->tiktokUrl(), 'external' => true],
    ])->filter(fn (array $channel) => $channel['value'] !== '' && $channel['href'] !== null);
@endphp

<section class="about-section about-contact" id="contact">
    <div class="lux-container about-contact__inner">
        <div class="about-contact__identity" data-reveal>
            <p class="about-contact__eyebrow">{{ $s->text('eyebrow') }}</p>

            <h2 class="about-contact__title">
                <span class="about-contact__title-line">{{ $s->text('title_1') }}</span>
                <span class="about-contact__title-line">{{ $s->text('title_2') }}</span>
            </h2>

            <p class="about-contact__company">{{ $site->companyName() }}</p>

            <address class="about-contact__address">
                @foreach ($site->headOfficeLines() as $line)
                    <span class="about-contact__address-line">{{ $line }}</span>
                @endforeach
            </address>
        </div>

        <div class="about-contact__channels" data-reveal data-reveal-delay="1">
            <h3 class="about-contact__channels-title">{{ $s->text('channels_title') }}</h3>

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
