<?php

namespace App\Filament\Resources\AnggotaResource\Pages;

use App\Filament\Resources\AnggotaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewAnggota extends ViewRecord
{
    protected static string $resource = AnggotaResource::class;

    protected static ?string $title = 'Detail Anggota';

    protected function getActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->url(static::getResource()::getUrl('index')),
        ];
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
}
