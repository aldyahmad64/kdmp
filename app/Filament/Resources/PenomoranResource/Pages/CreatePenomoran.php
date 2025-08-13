<?php

namespace App\Filament\Resources\PenomoranResource\Pages;

use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\PenomoranResource;

class CreatePenomoran extends CreateRecord
{
    protected static string $resource = PenomoranResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Buat Penomoran';

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
