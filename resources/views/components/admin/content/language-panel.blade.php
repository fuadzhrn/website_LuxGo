@props(['locale', 'id' => 'content-language', 'active' => false])

<div
    class="admin-tabs__panel"
    role="tabpanel"
    id="{{ $id }}-panel-{{ $locale }}"
    aria-labelledby="{{ $id }}-tab-{{ $locale }}"
    data-tab-panel="{{ $locale }}"
    tabindex="0"
    @unless ($active) hidden @endunless
>
    {{ $slot }}
</div>
