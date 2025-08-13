<?php

namespace App;

use App\Models\LogActivity;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            LogActivity::create([
                'team_id' => Filament::getTenant()?->id,
                'user_id' => Auth::id(),
                'deskripsi' => "Membuat data pada model " . get_class($model),
                'event' => 'created',
                'new_values' => $model->toArray(),
            ]);
        });

        static::updating(function ($model) {
            LogActivity::create([
                'team_id' => Filament::getTenant()?->id,
                'user_id' => Auth::id(),
                'deskripsi' => "Mengubah data pada model " . get_class($model),
                'event' => 'updated',
                'old_values' => $model->getOriginal(),
                'new_values' => $model->getAttributes(),
            ]);
        });

        static::deleting(function ($model) {
            LogActivity::create([
                'team_id' => Filament::getTenant()?->id,
                'user_id' => Auth::id(),
                'deskripsi' => "Menghapus data pada model " . get_class($model),
                'event' => 'deleted',
                'old_values' => $model->getOriginal(),
            ]);
        });
    }
}
