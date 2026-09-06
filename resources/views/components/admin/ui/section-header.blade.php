@props(['title', 'description' => null])

<div class="admin-section-header">
    <h2 class="admin-section-header__title">{{ $title }}</h2>

    @if ($description)
        <p class="admin-section-header__copy">{{ $description }}</p>
    @endif
</div>
