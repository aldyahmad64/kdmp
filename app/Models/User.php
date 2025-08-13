<?php

namespace App\Models;

use Filament\Panel;
use Illuminate\Support\Collection;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Filament\Models\Contracts\HasAvatar;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasTenants;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements HasAvatar, FilamentUser, HasTenants
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : url('/storage/avatars/default.png');
    }

    public static function booted(): void
    {
        self::forceDeleted(function (User $data) {
            if (!empty($data->avatar_url) && Storage::disk('public')->exists($data->avatar_url)) {
                Storage::disk('public')->delete($data->avatar_url);
            }
        });

        self::updating(function (User $data) {
            // Cek apakah file berubah
            if ($data->isDirty('avatar_url')) {
                $old = $data->getOriginal('avatar_url');
                if ($old && Storage::disk('public')->exists($old)) {
                    Storage::disk('public')->delete($old);
                }
            }
        });
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_users');
    }


    public function team(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_users');
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->team;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->team()->whereKey($tenant)->exists();
    }
}
