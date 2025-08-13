<?php

namespace App\Filament\Resources\AnggotaResource\Pages;

use Filament\Actions;
use App\Models\Penomoran;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\AnggotaResource;

class CreateAnggota extends CreateRecord
{
    protected static string $resource = AnggotaResource::class;

    public ?array $anggotaActivity = null;

    public ?array $pekerjaanData = null;

    public ?array $userData = null;

    public ?array $saldo = null;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Buat Anggota Baru';

    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }

    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label('Batal')
            ->url($this->getResource()::getUrl())
            ->color('gray')
            ->outlined();
    }

    public function create(bool $another = false): void
    {
        $data = $this->form->getState();

        if ($data['kode'] === "AUTO") {
            $nomor = Penomoran::where('name', 'ANGGOTA')->first();
            $counter = \App\Services\PenomoranServices::updateCounter($nomor);
            $fake = new Penomoran([
                'components' => $nomor->components ?? [],
                'counter' => $nomor->counter ?? $counter,
            ]);
            $data['kode'] = \App\Services\PenomoranServices::generate($fake);
            Penomoran::where('name', 'ANGGOTA')->increment('counter');
        }

        // $data['kode'] = $data['kode'] === "AUTO" ? strtotime(now()) : $data['kode'];
        // $anggotaActivity = $data;

        // Tangkap Data Saldo
        $saldoAnggota = $data['saldo'];
        unset($data['saldo']);

        // Tangkap Data Pekerjaan
        $pekerjaanData = $data['pekerjaan'];
        unset($data['pekerjaan']);

        // Tangkap Data Akun
        $data['user']['name'] = $data['nama'];
        $data['user']['kode'] = $data['kode'];
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

        $this->getCreatedNotification()?->send();

        $redirectUrl = $this->getRedirectUrl();

        $this->redirect($redirectUrl, navigate: \Filament\Support\Facades\FilamentView::hasSpaMode($redirectUrl));
    }
}
