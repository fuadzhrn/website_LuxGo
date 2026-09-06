<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /** Attempts allowed per email + IP combination before the lockout. */
    private const MAX_ATTEMPTS = 5;

    private const DECAY_SECONDS = 60;

    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            throw ValidationException::withMessages([
                'email' => __('Too many sign-in attempts. Please try again in :seconds seconds.', [
                    'seconds' => RateLimiter::availableIn($key),
                ]),
            ]);
        }

        /* Credentials are checked before any session is issued, and the role is
           checked before sign-in completes — so a non-administrator never gets
           an authenticated session that then has to be torn down. */
        if (! Auth::validate($credentials)) {
            RateLimiter::hit($key, self::DECAY_SECONDS);

            throw $this->genericFailure();
        }

        $user = Auth::getLastAttempted();

        if (! $user->isAdministrator()) {
            RateLimiter::hit($key, self::DECAY_SECONDS);

            throw $this->genericFailure();
        }

        Auth::login($user, $request->boolean('remember'));

        RateLimiter::clear($key);

        /* Fresh session id after a successful sign-in, so a session fixed before
           login cannot be reused. */
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * One message for every kind of failure — wrong password, unknown address,
     * or an account without admin rights — so the form never confirms which.
     */
    private function genericFailure(): ValidationException
    {
        return ValidationException::withMessages([
            'email' => __('These credentials do not match our records.'),
        ]);
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower((string) $request->input('email')).'|'.$request->ip()
        );
    }
}
