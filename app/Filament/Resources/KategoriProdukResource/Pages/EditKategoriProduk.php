<?php

namespace App\Filament\Resources\KategoriProdukResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\KategoriProdukResource;

class EditKategoriProduk extends EditRecord
{
    protected static string $resource = KategoriProdukResource::class;

    protected static ?string $title = 'Ubah Kategori Produk';

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
}
