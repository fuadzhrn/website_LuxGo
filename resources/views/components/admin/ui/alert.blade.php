@props(['type' => 'success', 'message' => null, 'dismissible' => true])

@php
    $message = $message ?? $slot;
    /* Errors are announced immediately; a save confirmation can wait its turn. */
    $live = $type === 'error' ? 'assertive' : 'polite';
@endphp

<div class="admin-alert admin-alert--{{ $type }}" role="status" aria-live="{{ $live }}" data-admin-alert>
    <p class="admin-alert__message">{{ $message }}</p>

    @if ($dismissible)
        <button type="button" class="admin-alert__dismiss" aria-label="Dismiss message" data-alert-dismiss>&times;</button>
    @endif
</div>
