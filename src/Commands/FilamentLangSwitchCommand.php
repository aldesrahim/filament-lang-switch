<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch\Commands;

use Illuminate\Console\Command;

final class FilamentLangSwitchCommand extends Command
{
    public $signature = 'filament-lang-switch';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
