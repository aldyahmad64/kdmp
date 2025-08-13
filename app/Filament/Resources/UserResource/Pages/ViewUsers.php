<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Actions;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;

class ViewUsers extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected static ?string $title = 'Detail User';

    protected function getActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
