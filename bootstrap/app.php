<?php

use App\Http\Middleware\EnsureUserIsAdministrator;
use App\Http\Middleware\NormalizeUnicode;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Internal tooling: registered outside the localized public routes.
            Route::middleware('web')->group(base_path('routes/admin.php'));

            /* robots.txt and sitemap.xml take no middleware at all, so neither
               opens a session nor sets a cookie that would stop a CDN caching
               them. */
            Route::middleware([])->group(base_path('routes/crawler.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'locale' => SetLocale::class,
            'administrator' => EnsureUserIsAdministrator::class,
        ]);

        /* Joins TrimStrings and ConvertEmptyStringsToNull: text pasted from Word
           carries Windows-1252 punctuation, which is not valid UTF-8 and renders
           as a replacement mark once stored. */
        $middleware->append(NormalizeUnicode::class);

        /* The only authenticated area is the admin panel, so both redirects
           point there rather than at Laravel's default /login and /dashboard. */
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));

        // URL defaults must be in place before bindings are substituted.
        $middleware->prependToPriorityList(
            before: SubstituteBindings::class,
            prepend: SetLocale::class,
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        /* A URL that matches no route never reaches SetLocale, so the 404 view
           would have no locale to build its links from. Work one out the way the
           entry point does: the prefix that was asked for, then the visitor's
           last choice, then the default. */
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return null;
            }

            $supported = config('locales.supported');

            /* An unmatched URL never ran the web group, so there may be no
               session store at all — asking for one would throw. */
            $remembered = $request->hasSession() ? $request->session()->get('locale') : null;

            $locale = collect([$request->segment(1), $remembered])
                ->first(fn ($candidate) => is_string($candidate) && in_array($candidate, $supported, true))
                ?? config('locales.default');

            App::setLocale($locale);
            URL::defaults(['locale' => $locale]);

            return response()->view('errors.404', [], 404);
        });
    })->create();
