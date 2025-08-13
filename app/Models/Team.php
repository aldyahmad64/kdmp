<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use Database\Seeders\SettingSeeder;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{

    public static function booted(): void
    {
        self::created(function (Team $data) {
            DB::transaction(function () use ($data) {
                $superAdmin = auth()->user()->find(1);
                $user = auth()->user();
                $allPermission = Permission::all();

                $roleSuperAdmin = Role::create([
                    'team_id' => $data->id,
                    'name' => 'SUPER ADMIN',
                    'guard_name' => 'web',
                ]);
                $roleSuperAdmin->syncPermissions($allPermission);
                TeamUser::create([
                    'team_id' => $data->id,
                    'user_id' => $superAdmin->id,
                ]);


                if (!$user->hasRole('SUPER ADMIN')) {
                    $roleUser = Role::create([
                        'team_id' => $data->id,
                        'name' => 'Admin',
                        'guard_name' => 'web',
                    ]);
                    $roleUser->syncPermissions($allPermission);
                    TeamUser::create([
                        'team_id' => $data->id,
                        'user_id' => $user->id,
                    ]);
                }

                app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($data->id);

                $superAdmin->assignRole($roleSuperAdmin);
                if (!$user->hasRole('SUPER ADMIN')) {
                    $user->assignRole($roleUser);
                }
                (new SettingSeeder())->runForTenant($data->id);
            });
        });

        static::deleting(function (Team $team) {
            $setting = DB::table('settings')->where('team_id', '=', $team->id)->where('key', '=', 'web_logo')->first();
            if ($setting->value != 'img/web/logo.png' && Storage::disk('public')->exists($setting->value)) {
                Storage::disk('public')->delete($setting->value);
            }
            $team->users()->detach();
            $usersWithoutTeam = User::whereDoesntHave('teams')->get();
            foreach ($usersWithoutTeam as $user) {
                $user->forceDelete();
            }
        });
    }

    public static function getTeam()
    {
        $data = DB::table('teams')->where('id', Filament::getTenant()->id)->first();
        return $data;
    }

    public function roles(): HasMany
    {
        return $this->hasMany(\App\Models\Role::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'team_users');
    }

    public function settings(): HasMany
    {
        return $this->hasMany(\App\Models\Setting::class);
    }

    public function penomorans(): HasMany
    {
        return $this->hasMany(\App\Models\Penomoran::class);
    }

    public function logActivities(): HasMany
    {
        return $this->hasMany(\App\Models\LogActivity::class);
    }

    public function anggotas(): HasMany
    {
        return $this->hasMany(\App\Models\Anggota::class);
    }

    public function kategoriProduks(): HasMany
    {
        return $this->hasMany(\App\Models\KategoriProduk::class);
    }

    public function units(): HasMany
    {
        return $this->hasMany(\App\Models\Unit::class);
    }


    /** @return HasMany<\App\Models\Produk, self> */
    public function produks(): HasMany
    {
        return $this->hasMany(\App\Models\Produk::class);
    }


    /** @return HasMany<\App\Models\TipeHarga, self> */
    public function tipeHargas(): HasMany
    {
        return $this->hasMany(\App\Models\TipeHarga::class);
    }

}
