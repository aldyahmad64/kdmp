<?php

namespace App\Filament\Resources\KategoriProdukResource\Pages;

use App\Filament\Resources\KategoriProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewKategoriProduk extends ViewRecord
{
    protected static string $resource = KategoriProdukResource::class;

    protected static ?string $title = 'Detail Kategori Produk';

    protected function getActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->url(static::getResource()::getUrl('index')),
        ];
    }

}
