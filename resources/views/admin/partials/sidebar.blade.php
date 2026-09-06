@php
    /* Active state comes from the route name, so a page never has to pass its
       own flag and renaming a URL cannot leave the menu out of step. */
    $adminNavGroups = [
        [
            'label' => null,
            'items' => [
                ['label' => 'Dashboard', 'route' => 'admin.dashboard'],
            ],
        ],
        [
            'label' => 'Content',
            'items' => [
                ['label' => 'Content', 'route' => 'admin.content'],
            ],
        ],
        [
            'label' => 'Leads',
            'items' => [
                ['label' => 'Membership Applications', 'route' => 'admin.applications'],
            ],
        ],
        [
            'label' => 'Media',
            'items' => [
                ['label' => 'Media', 'route' => 'admin.media'],
            ],
        ],
        [
            'label' => 'Website',
            'items' => [
                ['label' => 'SEO', 'route' => 'admin.seo'],
                ['label' => 'Settings', 'route' => 'admin.settings'],
            ],
        ],
        [
            'label' => 'Account',
            'items' => [
                ['label' => 'Profile', 'route' => 'admin.profile'],
            ],
        ],
    ];
@endphp

<aside class="admin-sidebar" id="admin-sidebar" data-admin-sidebar>
    <div class="admin-sidebar__brand">
        <span class="admin-sidebar__wordmark">LUX&amp;GO</span>
        <span class="admin-sidebar__kicker">Admin Panel</span>
    </div>

    <nav class="admin-sidebar__nav" aria-label="Admin navigation">
        @foreach ($adminNavGroups as $group)
            <div class="admin-sidebar__group">
                @if ($group['label'])
                    <p class="admin-sidebar__group-label">{{ $group['label'] }}</p>
                @endif

                <ul class="admin-sidebar__list">
                    @foreach ($group['items'] as $item)
                        @php($isActive = request()->routeIs($item['route']))
                        <li>
                            <a
                                href="{{ route($item['route']) }}"
                                class="admin-sidebar__link{{ $isActive ? ' is-active' : '' }}"
                                @if ($isActive) aria-current="page" @endif
                            >{{ $item['label'] }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </nav>

    <form method="POST" action="{{ route('admin.logout') }}" class="admin-sidebar__logout">
        @csrf
        <button type="submit" class="admin-sidebar__logout-button">Log out</button>
    </form>
</aside>

<div class="admin-sidebar__scrim" data-admin-scrim hidden></div>
