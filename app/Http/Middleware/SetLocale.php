<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Resolve the locale from the URL prefix and make it the request-wide default.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route('locale');

        /* The route constraint already filters these; this is the second gate so
           an unsupported locale can never be silently accepted. */
        abort_unless(
            is_string($locale) && in_array($locale, config('locales.supported'), true),
            404
        );

        App::setLocale($locale);

        /* Lets every existing route() call keep the active locale without each
           Blade having to pass it. */
        URL::defaults(['locale' => $locale]);

        $request->session()->put('locale', $locale);

        return $next($request);
    }
}
