<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\Permission;
use App\Models\TeamUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat Semua Permission
        Artisan::call("shield:generate", [
            "--all" => true,
            "--panel" => "admin"
        ]);

        // Get All Perrmission
        $allPermission = Permission::all();

        // Buat Team
        $team = DB::table("teams")->insertGetId(['name' => 'Main', 'slug' => 'main']);

        // Buat Role
        $role = Role::create(['team_id' => $team, 'name' => 'SUPER ADMIN', 'guard_name' => 'web']);
        $role->syncPermissions($allPermission);

        // Buat User
        $user = User::create([
            'name' => 'SUPER ADMIN',
            'email' => 'superadmin@admin.com',
            'password' => \Illuminate\Support\Facades\Hash::make('superadmin'),
        ]);

        // Buat TeamUser
        TeamUser::create(['team_id' => $team, 'user_id' => $user->id]);

        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($team);
        $user->assignRole($role);


        $this->call(SettingSeeder::class);
    }
}
