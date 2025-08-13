<?php

namespace App\Filament\Resources\ProdukResource\Pages;

use App\Filament\Resources\ProdukResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProduk extends ViewRecord
{
    protected static string $resource = ProdukResource::class;

    protected static ?string $title = 'Detail Produk';

    protected function getActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
