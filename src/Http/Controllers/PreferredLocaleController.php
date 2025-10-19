<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch\Http\Controllers;

use Illuminate\Http\RedirectResponse;

final class PreferredLocaleController
{
    public function __invoke(string $locale): RedirectResponse
    {
        app('filament-lang-switch')->setPreferredLocale($locale);

        return back();
    }
}
