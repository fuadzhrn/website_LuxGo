@props([
    'name' => 'is_active',
    'label' => 'Status',
    'checked' => true,
    'help' => null,
    /* The two states in words. They default to Active/Inactive because most
       toggles switch a section on and off, but a toggle that means something
       else says so. */
    'onLabel' => 'Active',
    'offLabel' => 'Inactive',
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
        <span
            class="admin-toggle__text"
            data-status-label
            data-status-on="{{ $onLabel }}"
            data-status-off="{{ $offLabel }}"
        >{{ $isOn ? $onLabel : $offLabel }}</span>
    </label>

    @if ($help)
        <p class="admin-help">{{ $help }}</p>
    @endif
</div>
