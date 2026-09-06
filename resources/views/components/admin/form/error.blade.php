@props(['name', 'id' => null])

@php
    /* content[id][title] and content.id.title address the same value; the error
       bag only knows the dotted form. */
    $key = trim(str_replace(['[', ']'], ['.', ''], $name), '.');
@endphp

@error($key)
    <p class="admin-error" @if ($id) id="{{ $id }}" @endif>{{ $message }}</p>
@enderror
