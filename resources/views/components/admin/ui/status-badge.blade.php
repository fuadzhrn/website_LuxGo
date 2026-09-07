@props(['status', 'label' => null])

@php
    /* One restrained tone per state: a lead that needs attention reads as
       active, a finished one as settled, and a closed one as quiet. */
    $tone = match ($status) {
        'new' => 'on',
        'completed' => 'done',
        'rejected' => 'muted',
        default => 'neutral',
    };

    $label = $label ?? Str::headline($status);
@endphp

<span {{ $attributes->class(['admin-pill', 'admin-pill--'.$tone]) }}>{{ $label }}</span>
