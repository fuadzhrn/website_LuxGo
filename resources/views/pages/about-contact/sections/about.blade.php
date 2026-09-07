@php
    /* Five audiences, fixed by the layout — the CMS supplies their wording. */
    $audiences = [
        ['number' => '01', 'label' => $s->text('audiences.business')],
        ['number' => '02', 'label' => $s->text('audiences.executives')],
        ['number' => '03', 'label' => $s->text('audiences.families')],
        ['number' => '04', 'label' => $s->text('audiences.professionals')],
        ['number' => '05', 'label' => $s->text('audiences.corporate')],
    ];
@endphp

<section class="about-section about-intro">
    <div class="lux-container about-intro__inner">
        <div class="about-intro__lead">
            <p class="about-intro__eyebrow" data-enter>{{ $s->text('eyebrow') }}</p>

            <h1 class="about-intro__title" data-enter data-enter-delay="1">
                <span class="about-intro__title-line">{{ $s->text('title_1') }}</span>
                <span class="about-intro__title-line">{{ $s->text('title_2') }}</span>
            </h1>

            <p class="about-intro__copy" data-enter data-enter-delay="2">
                {{ $s->text('copy') }}
            </p>
        </div>

        <div class="about-intro__serve" data-enter data-enter-delay="3">
            <h2 class="about-intro__serve-title">{{ $s->text('serve_title') }}</h2>

            {{-- Five audiences as a numbered editorial register — never five cards. --}}
            <ol class="about-intro__list">
                @foreach ($audiences as $audience)
                    <li class="about-intro__item">
                        <span class="about-intro__number">{{ $audience['number'] }}</span>
                        <span class="about-intro__label">{{ $audience['label'] }}</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>
