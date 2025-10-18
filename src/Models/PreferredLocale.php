<?php

declare(strict_types=1);

namespace Aldesrahim\FilamentLangSwitch\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

final class PreferredLocale extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = [
        'model_type',
        'model_id',
        'locale',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
