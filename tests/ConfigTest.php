<?php

declare(strict_types=1);

it('can retrieve config', function () {
    $config = config('filament-lang-switch');

    expect($config)->toBeArray()
        ->and($config)->toHaveKey('tables')
        ->and($config)->toHaveKey('models');
});
