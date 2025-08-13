<?php

namespace App\Filament\Resources\AnggotaResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\AnggotaResource;

class ListAnggotas extends ListRecords
{
    protected static string $resource = AnggotaResource::class;

    public ?array $anggotaActivity = null;

    public ?array $pekerjaanData = null;

    public ?array $userData = null;

    public ?array $saldoAnggota = null;

    protected static ?string $title = 'List Anggota';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Anggota Baru')
                ->modalWidth(MaxWidth::Full)
                ->slideOver()
                ->createAnother(false)
                ->using(function (array $data) {
                    $data['kode'] = strtotime(now());
                    $anggotaActivity = $data;

                    // Tangkap Data Saldo
                    $saldoAnggota = $data['saldo'];
                    unset($data['saldo']);

                    // Tangkap Data Pekerjaan
                    $pekerjaanData = $data['pekerjaan'];
                    unset($data['pekerjaan']);

                    // Tangkap Data Akun
                    $data['user']['name'] = $data['nama'];
                    $userData = $data['user'];
                    unset($data['user']);

                    // Buat Akun
                    $user = \App\Models\User::create($userData);
                    $user->assignRole('Anggota');

                    // Proses Data Anggota
                    $data['user_id'] = $user->id;
                    $anggota = \App\Models\Anggota::create($data);

                    // Proses Data Pekerjaan
                    $anggota->pekerjaan()->create($pekerjaanData);

                    // Proses Data Saldo
                    $anggota->saldo()->create($saldoAnggota);

                    // AnggotaActivity::createAnggota($anggotaActivity);
        
                    $redirectUrl = static::getResource()::getUrl('index');

                    $this->redirect($redirectUrl, navigate: \Filament\Support\Facades\FilamentView::hasSpaMode($redirectUrl));
                }),
        ];
    }
}
