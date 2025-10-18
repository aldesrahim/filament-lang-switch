<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Aldesrahim\FilamentLangSwitch\FilamentLangSwitch
 */
final class FilamentLangSwitch extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Aldesrahim\FilamentLangSwitch\FilamentLangSwitch::class;
    }
}
