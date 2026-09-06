@props([
    'name' => 'is_active',
    'label' => 'Status',
    'checked' => true,
    'help' => null,
])

@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = 'f-'.Str::slug(str_replace('.', '-', $key));
    $isOn = (bool) old($key, $checked);
@endphp

<div class="admin-field">
    <p class="admin-label" id="{{ $id }}-label">{{ $label }}</p>

    {{-- The hidden input means an unchecked box still posts a value, so a
         section can actually be switched off. --}}
    <input type="hidden" name="{{ $name }}" value="0">

    <label class="admin-toggle" for="{{ $id }}">
        <input
            class="admin-toggle__input"
            type="checkbox"
            id="{{ $id }}"
            name="{{ $name }}"
            value="1"
            @checked($isOn)
            data-status-toggle
        >
        <span class="admin-toggle__track" aria-hidden="true"><span class="admin-toggle__thumb"></span></span>
        <span class="admin-toggle__text" data-status-label>{{ $isOn ? 'Active' : 'Inactive' }}</span>
    </label>

    @if ($help)
        <p class="admin-help">{{ $help }}</p>
    @endif
</div>
