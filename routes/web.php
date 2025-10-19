<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

Route::get('{locale}', config('filament-lang-switch.routes.preferred_locale.action'))
    ->prefix(config('filament-lang-switch.routes.preferred_locale.prefix'))
    ->name(config('filament-lang-switch.routes.preferred_locale.name'))
    ->middleware(config('filament-lang-switch.routes.preferred_locale.middleware'));
