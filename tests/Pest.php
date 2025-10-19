<?php

declare(strict_types=1);

use Aldesrahim\FilamentLangSwitch\Tests\TestCase;

uses(TestCase::class)->in(__DIR__);

function getPreferredLocaleService(): Aldesrahim\FilamentLangSwitch\FilamentLangSwitch
{
    return app('filament-lang-switch');
}

function setAvailableLocales(): void
{
    config()->set('filament-lang-switch.available_locales', [
        'en' => 'English',
        'id' => 'Bahasa Indonesia',
    ]);
}
