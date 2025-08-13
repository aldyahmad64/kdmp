<?php

namespace App\Filament\Resources\ProdukResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProdukResource;

class ListProduks extends ListRecords
{
    protected static string $resource = ProdukResource::class;

    protected static ?string $title = 'List Produk';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Produk Baru')
                ->modalWidth(MaxWidth::Full)
                ->slideOver()
                ->createAnother(false),
        ];
    }
}
