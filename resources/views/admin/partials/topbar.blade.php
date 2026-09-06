<header class="admin-topbar">
    <button
        type="button"
        class="admin-topbar__toggle"
        data-admin-toggle
        aria-label="Open navigation"
        aria-expanded="false"
        aria-controls="admin-sidebar"
    >
        <span class="admin-topbar__toggle-bar" aria-hidden="true"></span>
        <span class="admin-topbar__toggle-bar" aria-hidden="true"></span>
        <span class="admin-topbar__toggle-bar" aria-hidden="true"></span>
    </button>

    <h1 class="admin-topbar__title">@yield('title', 'Admin')</h1>

    <div class="admin-topbar__user">
        <span class="admin-topbar__name">{{ auth()->user()->name }}</span>
        <span class="admin-topbar__role">{{ ucfirst(auth()->user()->role) }}</span>
    </div>
</header>
