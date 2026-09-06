<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Admin') — LUX&amp;GO</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/forms.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/components.css') }}">
</head>
<body class="admin">

    @include('admin.partials.sidebar')

    <div class="admin__main">
        @include('admin.partials.topbar')

        <main class="admin__content">
            {{-- Any controller can report an outcome with a session flash. --}}
            <x-admin.ui.flash />

            @yield('content')
        </main>
    </div>

    <script src="{{ asset('assets/js/admin/admin.js') }}" defer></script>
    <script src="{{ asset('assets/js/admin/components.js') }}" defer></script>

</body>
</html>
