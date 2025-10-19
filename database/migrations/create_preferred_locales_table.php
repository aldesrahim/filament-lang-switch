<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = config('filament-lang-switch.tables.preferred_locale');
        Schema::create($tableName, function (Blueprint $table): void {
            $table->id();

            $table->morphs('model');
            $table->string('locale', 5);

            $table->timestamps();
        });
    }
};
