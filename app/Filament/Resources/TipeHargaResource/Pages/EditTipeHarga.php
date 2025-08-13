<?php

namespace App\Filament\Resources\TipeHargaResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\TipeHargaResource;

class EditTipeHarga extends EditRecord
{
    protected static string $resource = TipeHargaResource::class;

    protected static ?string $title = 'Ubah Jenis Harga Produk';

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
