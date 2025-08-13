<?php

namespace App\Filament\Resources\KategoriProdukResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\KategoriProdukResource;

class CreateKategoriProduk extends CreateRecord
{
    protected static string $resource = KategoriProdukResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Buat Kategori Produk Baru';

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
