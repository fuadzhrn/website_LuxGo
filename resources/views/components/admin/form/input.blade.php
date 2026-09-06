@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'help' => null,
    'id' => null,
])

@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = $id ?? 'f-'.Str::slug(str_replace('.', '-', $key));
    $hasError = $errors->has($key);
    $describedBy = collect([$help ? $id.'-help' : null, $hasError ? $id.'-error' : null])
        ->filter()->implode(' ');
@endphp

<div class="admin-field">
    <label class="admin-label" for="{{ $id }}">
        {{ $label }}@if ($required)<span class="admin-label__required" title="Required">*</span>@endif
    </label>

    <input
        {{ $attributes->class(['admin-input', 'has-error' => $hasError]) }}
        type="{{ $type }}"
        id="{{ $id }}"
        name="{{ $name }}"
        value="{{ old($key, $value) }}"
        @if ($placeholder) placeholder="{{ $placeholder }}" @endif
        @if ($required) required @endif
        @if ($disabled) disabled @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if ($hasError) aria-invalid="true" @endif
    >

    @if ($help)
        <p class="admin-help" id="{{ $id }}-help">{{ $help }}</p>
    @endif

    <x-admin.form.error :name="$name" :id="$id.'-error'" />
</div>
