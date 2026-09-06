<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Sign In — LUX&amp;GO Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Manrope:wght@500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('assets/css/admin/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/admin/login.css') }}">
</head>
<body class="admin-login">

    <main class="admin-login__panel">
        <div class="admin-login__brand">
            <span class="admin-login__wordmark">LUX&amp;GO</span>
            <span class="admin-login__kicker">Admin Panel</span>
        </div>

        <h1 class="admin-login__title">Sign in</h1>

        @if ($errors->any())
            <p class="admin-login__error" role="alert">{{ $errors->first() }}</p>
        @endif

        <form method="POST" action="{{ route('admin.login.store') }}" class="admin-login__form">
            @csrf

            <div class="admin-field">
                <label class="admin-label" for="email">Email</label>
                <input
                    class="admin-input"
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    autocomplete="username"
                    required
                    autofocus
                >
            </div>

            <div class="admin-field">
                <label class="admin-label" for="password">Password</label>
                <input
                    class="admin-input"
                    type="password"
                    id="password"
                    name="password"
                    autocomplete="current-password"
                    required
                >
            </div>

            <label class="admin-login__remember">
                <input type="checkbox" name="remember" value="1">
                <span>Remember me</span>
            </label>

            <button type="submit" class="admin-button admin-button--primary admin-login__submit">Sign In</button>
        </form>
    </main>

</body>
</html>
