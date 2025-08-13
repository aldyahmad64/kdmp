<?php

namespace App\Filament\Resources\KategoriProdukResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\KategoriProdukResource;

class ListKategoriProduks extends ListRecords
{
    protected static string $resource = KategoriProdukResource::class;

    protected static ?string $title = 'List Kategori Produk';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Kategori Produk Baru')
                ->modalWidth(MaxWidth::Full)
                ->slideOver()
                ->createAnother(false),
        ];
    }
}
