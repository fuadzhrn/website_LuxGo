@props(['title', 'copy' => null, 'note' => null])

<div class="admin-empty">
    <h2 class="admin-empty__title">{{ $title }}</h2>

    @if ($copy)
        <p class="admin-empty__copy">{{ $copy }}</p>
    @endif

    @if ($note)
        <p class="admin-empty__note">{{ $note }}</p>
    @endif

    {{ $slot }}
</div>
