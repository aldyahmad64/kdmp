<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        \DB::table('settings')->delete();
        $this->runForTenant(1);
    }
    /**
     * Run the database seeds.
     */
    public function runForTenant($id)
    {
        \DB::table('settings')->insert([
            [
                'team_id' => $id,
                'key' => 'web_name',
                'deskripsi' => 'Nama atau title untuk website',
                'type' => 'text',
                'value' => 'MASTER',
                'tab' => 'WEB SETTING',
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'team_id' => $id,
                'key' => 'web_spa',
                'deskripsi' => 'Loading Tanpa Refresh Page',
                'type' => 'boolean',
                'value' => true,
                'tab' => 'WEB SETTING',
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'team_id' => $id,
                'key' => 'form_slide',
                'deskripsi' => 'Tampilan Form Slide',
                'type' => 'boolean',
                'value' => false,
                'tab' => 'WEB SETTING',
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'team_id' => $id,
                'key' => 'web_logo',
                'deskripsi' => 'Logo atau icon untuk website',
                'type' => 'gambar',
                'value' => 'img/web/logo.png',
                'tab' => 'WEB SETTING',
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'team_id' => $id,
                'key' => 'admin_warna_primary',
                'deskripsi' => 'Warna primary',
                'type' => 'warna',
                'value' => 'rgb(0,0,255)',
                'tab' => 'WEB SETTING',
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'team_id' => $id,
                'key' => 'admin_warna_gray',
                'deskripsi' => 'Warna gray',
                'type' => 'warna',
                'value' => 'rgb(128,128,128)',
                'tab' => 'WEB SETTING',
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'team_id' => $id,
                'key' => 'admin_warna_warning',
                'deskripsi' => 'Warna warning',
                'type' => 'warna',
                'value' => 'rgb(255,252,0)',
                'tab' => 'WEB SETTING',
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'team_id' => $id,
                'key' => 'admin_warna_info',
                'deskripsi' => 'Warna info',
                'type' => 'warna',
                'value' => 'rgb(137,247,54)',
                'tab' => 'WEB SETTING',
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'team_id' => $id,
                'key' => 'admin_warna_danger',
                'deskripsi' => 'Warna danger',
                'type' => 'warna',
                'value' => 'rgb(255,0,0)',
                'tab' => 'WEB SETTING',
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'team_id' => $id,
                'key' => 'admin_warna_success',
                'deskripsi' => 'Warna success',
                'type' => 'warna',
                'value' => 'rgb(0,255,0)',
                'tab' => 'WEB SETTING',
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'team_id' => $id,
                'key' => 'bunga_pinjaman',
                'deskripsi' => 'Bunga Pinjaman',
                'type' => 'persen',
                'value' => '5',
                'tab' => 'SIMPAN PINJAM',
                'is_default' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
