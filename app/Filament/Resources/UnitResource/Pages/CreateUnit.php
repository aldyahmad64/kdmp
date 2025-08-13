<?php

namespace App\Filament\Resources\UnitResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use App\Filament\Resources\UnitResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Buat Satuan Produk Baru';

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
