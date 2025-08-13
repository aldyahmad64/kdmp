<?php

namespace App\Filament\Resources\TipeHargaResource\Pages;

use App\Filament\Resources\TipeHargaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTipeHarga extends ViewRecord
{
    protected static string $resource = TipeHargaResource::class;

    protected static ?string $title = 'Detail Jenis Harga Produk';

    protected function getActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
