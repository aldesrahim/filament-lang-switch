<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class PreferredLocaleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $preferredLocale = app('filament-lang-switch')->getPreferredLocale();

        app()->setLocale($preferredLocale ?? config('app.locale'));

        return $next($request);
    }
}
