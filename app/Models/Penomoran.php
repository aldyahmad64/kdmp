<?php

namespace App\Models;

use Filament\Facades\Filament;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Penomoran extends Model
{
    use HasRoles;

    public static function booted(): void
    {
        static::addGlobalScope('team', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->where('team_id', Filament::getTenant()?->id);
            }
        });
    }

    protected $casts = [
        'components' => 'array', // ✅ Ini penting!
    ];

    /** @return BelongsTo<\App\Models\Team, self> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Team::class);
    }
}
