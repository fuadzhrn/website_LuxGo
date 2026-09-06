@props([
    'name',
    'label',
    'options' => [],
    'value' => null,
    'required' => false,
    'help' => null,
    'id' => null,
])

@php
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
    $id = $id ?? 'f-'.Str::slug(str_replace('.', '-', $key));
    $hasError = $errors->has($key);
    $selected = old($key, $value);
    $describedBy = collect([$help ? $id.'-help' : null, $hasError ? $id.'-error' : null])
        ->filter()->implode(' ');
@endphp

{{-- A closed list: the admin picks a destination that already exists rather
     than typing a URL, so a link cannot leave the site or carry a script. --}}
<div class="admin-field">
    <label class="admin-label" for="{{ $id }}">
        {{ $label }}@if ($required)<span class="admin-label__required" title="Required">*</span>@endif
    </label>

    <select
        {{ $attributes->class(['admin-input', 'admin-select', 'has-error' => $hasError]) }}
        id="{{ $id }}"
        name="{{ $name }}"
        @if ($required) required @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        @if ($hasError) aria-invalid="true" @endif
    >
        @foreach ($options as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) $selected === (string) $optionValue)>{{ $optionLabel }}</option>
        @endforeach
    </select>

    @if ($help)
        <p class="admin-help" id="{{ $id }}-help">{{ $help }}</p>
    @endif

    <x-admin.form.error :name="$name" :id="$id.'-error'" />
</div>
