<?php

namespace App\Filament\Resources\TipeHargaResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TipeHargaResource;

class ListTipeHargas extends ListRecords
{
    protected static string $resource = TipeHargaResource::class;

    protected static ?string $title = 'List Jenis Harga Produk';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Jenis Harga Baru')
                ->modalWidth(MaxWidth::Full)
                ->slideOver()
                ->createAnother(false),
        ];
    }
}
