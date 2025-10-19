<?php

declare(strict_types=1);

return [
    'available_locales' => [
        'en' => ['label' => 'English'],
        'id' => ['label' => 'Bahasa Indonesia'],
    ],

    'stores' => [
        'cookie' => [
            'cookie_name' => 'filament_lang_switch_locale',
            'minutes' => 60 * 24,
        ],

        'session' => [
            'session_key' => 'filament_lang_switch_locale',
        ],

        'user' => [
            'guard' => 'web',
            'cache' => [
                'enabled' => false,
                'prefix' => 'filament_lang_switch_locale_',
                'minutes' => 10,
            ],
        ],
    ],

    'tables' => [
        'preferred_locale' => 'preferred_locales',
    ],

    'models' => [
        'preferred_locale' => Aldesrahim\FilamentLangSwitch\Models\PreferredLocale::class,
    ],

    'routes' => [
        'preferred_locale' => [
            'action' => Aldesrahim\FilamentLangSwitch\Http\Controllers\PreferredLocaleController::class,
            'prefix' => 'preferred-locale',
            'name' => 'preferred-locale.',
            'middleware' => ['web'],
        ],
    ],

    'middleware' => [
        'preferred_locale' => Aldesrahim\FilamentLangSwitch\Http\Middleware\PreferredLocaleMiddleware::class,
    ],
];
