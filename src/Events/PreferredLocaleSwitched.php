<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class PreferredLocaleSwitched
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public readonly string $locale) {}
}
