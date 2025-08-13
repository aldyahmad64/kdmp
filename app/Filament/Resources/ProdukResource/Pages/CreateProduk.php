<?php

namespace App\Filament\Resources\ProdukResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Filament\Resources\ProdukResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProduk extends CreateRecord
{
    protected static string $resource = ProdukResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Buat Produk Baru';

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
}
