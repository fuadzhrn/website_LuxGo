@props(['label' => 'Save changes', 'updatedAt' => null, 'disabled' => false])

<div class="admin-save">
    <button
        type="submit"
        class="admin-button admin-button--primary"
        @disabled($disabled)
        data-admin-submit
    >{{ $label }}</button>

    @if ($updatedAt)
        <p class="admin-save__meta">Last updated {{ $updatedAt->format('d M Y, H:i') }}</p>
    @endif

    {{ $slot }}
</div>
