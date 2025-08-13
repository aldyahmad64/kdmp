<?php

namespace App\Filament\Resources\UnitResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Filament\Resources\UnitResource;
use Filament\Resources\Pages\EditRecord;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    protected static ?string $title = 'Ubah Satuan Produk';

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
