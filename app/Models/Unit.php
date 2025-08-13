<?php

namespace App\Models;

use App\Models\Team;
use App\Models\ProdukUnit;
use App\Models\ProdukHarga;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Unit extends Model
{
    use SoftDeletes;

    public static function booted(): void
    {
        static::addGlobalScope('team', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->where('team_id', Filament::getTenant()?->id);
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function produkUnits()
    {
        return $this->hasMany(ProdukUnit::class);
    }

    public function produkHargas()
    {
        return $this->hasMany(ProdukHarga::class);
    }
}
