<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch\Models;

use Illuminate\Database\Eloquent\Model;

final class PreferredLocale extends Model
{
    protected $fillable = [
        'model_type',
        'model_id',
        'locale',
    ];
}
