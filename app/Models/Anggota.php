<?php

namespace App\Models;

use Filament\Facades\Filament;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anggota extends Model
{
    use HasRoles, SoftDeletes;

    protected $casts = [
        'lampiran' => 'array',
    ];

    public static function booted(): void
    {
        static::addGlobalScope('team', function (Builder $query) {
            if (auth()->hasUser()) {
                $query->where('team_id', Filament::getTenant()?->id);
            }
        });

        static::creating(function (Anggota $model) {
            if (auth()->check() && Filament::getTenant()) {
                $team = Filament::getTenant()->id;
                $model->team_id = $team;
                TeamUser::create(['team_id' => $team, 'user_id' => $model->user->id]);
            }
        });
        self::updated(function (\App\Models\Anggota $data) {
            // Hapus foto lama jika berubah
            if ($data->isDirty('foto')) {
                $old = $data->getOriginal('foto');
                if ($old && $old !== "img/web/logo.png" && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
            }

            // Hapus file lampiran lama jika berubah
            if ($data->isDirty('lampiran')) {
                $oldLampiran = $data->getOriginal('lampiran') ?? [];
                $newLampiran = $data->lampiran ?? [];
                if (is_array($oldLampiran) && is_array($newLampiran)) {
                    $deletedFiles = array_diff($oldLampiran, $newLampiran);
                    foreach ($deletedFiles as $file) {
                        if ($file && Storage::disk('public')->exists($file)) {
                            Storage::disk('public')->delete($file);
                        }
                    }
                }
            }
        });

        self::deleted(function (\App\Models\Anggota $data) {
            // Hapus Sementara Akun Jika Anggota Dihapus Sementara
            \App\Models\User::find($data->user_id)?->delete();
        });

        self::restored(function (\App\Models\Anggota $data) {
            // Restore Akun Jika Anggota Direstore
            \App\Models\User::withTrashed()->find($data->user_id)?->restore();
        });

        self::forceDeleted(function (\App\Models\Anggota $data) {
            // Hapus Permanen Akun Jika Anggota Dihapus Permanen
            \App\Models\User::withTrashed()->find($data->user_id)?->forceDelete();

            // Hapus file foto (jika bukan default)
            if ($data->foto && $data->foto !== "img/web/logo.png") {
                if (Storage::disk('public')->exists($data->foto)) {
                    Storage::disk('public')->delete($data->foto);
                }
            }

            // Hapus semua file lampiran
            if ($data->lampiran) {
                foreach ($data->lampiran as $file) {
                    if (Storage::disk('public')->exists($file)) {
                        Storage::disk('public')->delete($file);
                    }
                }
            }
        });
    }

    /** @return BelongsTo<\App\Models\Team, self> */
    public function team(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function pekerjaan(): HasOne
    {
        return $this->hasOne(Pekerjaan::class);
    }

    public function saldo(): HasOne
    {
        return $this->hasOne(Saldo::class);
    }
}
