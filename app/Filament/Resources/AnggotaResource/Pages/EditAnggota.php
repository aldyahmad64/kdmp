<?php

namespace App\Filament\Resources\AnggotaResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\AnggotaResource;

class EditAnggota extends EditRecord
{
    protected static string $resource = AnggotaResource::class;

    protected static ?string $title = 'Ubah Anggota';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

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

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Siapkan Data Pekerjaan Sebelum Dimuat
        $pekerjaans = $this->record->pekerjaan;
        $data['pekerjaan'] = [
            'deskripsi' => $pekerjaans->deskripsi ?? null,
            'alamat' => $pekerjaans->alamat ?? null,
            'penghasilan' => $pekerjaans->penghasilan ?? null,
        ];
        // Siapkan Data Saldo Sebelum Dimuat
        $saldoAnggota = $this->record->saldo;
        $data['saldo'] = [
            'saldo_pokok' => $saldoAnggota->saldo_pokok ?? null,
            'saldo_wajib' => $saldoAnggota->saldo_wajib ?? null,
            'saldo_sukarela' => $saldoAnggota->saldo_sukarela ?? null,
            'saldo_bonus' => $saldoAnggota->saldo_bonus ?? null,
            'saldo_total' => $saldoAnggota->saldo_total ?? null,
        ];
        // Kirimkan Hasil
        return $data;
    }

    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $data = $this->form->getState();
        $anggotaActivityOld = $this->record;
        $anggotaActivityOld['pekerjaan'] = \Illuminate\Support\Arr::except($this->record->pekerjaan, ['id', 'anggota_id', 'created_at', 'updated_at']);
        $anggotaActivityOld['saldo'] = \Illuminate\Support\Arr::except($this->record->saldo, ['id', 'anggota_id', 'created_at', 'updated_at']);
        $anggotaActivityNew = $data;
        // Hapus Data Saldo Dari Array
        unset($data['saldo']);

        // Tangkap Data Pekerjaan
        $pekerjaanData = $data['pekerjaan'];
        unset($data['pekerjaan']);

        // Tangkap Data Akun
        unset($data['user']['email']);
        $data['user']['name'] = $data['nama'];
        $userData = $data['user'];
        unset($data['user']);

        // Ubah Data Akun
        \App\Models\User::withTrashed()->find($this->record->user_id)->update($userData);

        // Ubah Data Anggota
        $anggotaModel = \App\Models\Anggota::withTrashed()->find($this->record->id);
        $anggotaModel->update($data);

        // Ubah Data Pekerjaan
        $anggotaModel->pekerjaan->update($pekerjaanData);

        // AnggotaActivity::updateAnggota($anggotaActivityOld, $anggotaActivityNew);

        $this->getSavedNotification()?->send();

        $redirectUrl = $this->getRedirectUrl();
        $this->redirect($redirectUrl, navigate: \Filament\Support\Facades\FilamentView::hasSpaMode($redirectUrl));
    }
}
