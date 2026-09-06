@props(['id' => 'content-language'])

@php
    $locales = config('locales.supported');
    $labels = config('admin.locale_labels');
@endphp

{{-- Only translated text belongs inside these panels. Shared values — imagery,
     status, prices — sit outside so they are never entered twice. --}}
<div class="admin-tabs" data-admin-tabs>
    <p class="admin-tabs__label" id="{{ $id }}-label">Content language</p>

    <div class="admin-tabs__list" role="tablist" aria-labelledby="{{ $id }}-label">
        @foreach ($locales as $index => $locale)
            <button
                type="button"
                class="admin-tabs__tab{{ $index === 0 ? ' is-active' : '' }}"
                role="tab"
                id="{{ $id }}-tab-{{ $locale }}"
                aria-controls="{{ $id }}-panel-{{ $locale }}"
                aria-selected="{{ $index === 0 ? 'true' : 'false' }}"
                tabindex="{{ $index === 0 ? '0' : '-1' }}"
                data-tab-target="{{ $locale }}"
            >{{ $labels[$locale] ?? strtoupper($locale) }}</button>
        @endforeach
    </div>

    <div class="admin-tabs__panels">
        {{ $slot }}
    </div>
</div>
