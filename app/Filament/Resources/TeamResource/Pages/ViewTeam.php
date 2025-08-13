<?php

namespace App\Filament\Resources\TeamResource\Pages;

use Filament\Actions;
use App\Filament\Resources\TeamResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTeam extends ViewRecord
{
    protected static string $resource = TeamResource::class;

    protected static ?string $title = 'Detail Team';

    protected function getActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
