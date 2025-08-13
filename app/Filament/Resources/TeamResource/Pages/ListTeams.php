<?php

namespace App\Filament\Resources\TeamResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\TeamResource;
use Filament\Resources\Pages\ListRecords;

class ListTeams extends ListRecords
{
    protected static string $resource = TeamResource::class;

    protected static ?string $title = 'List Team';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Team Baru')
                ->modalWidth(MaxWidth::Full)
                ->slideOver()
                ->createAnother(false),
        ];
    }
}
