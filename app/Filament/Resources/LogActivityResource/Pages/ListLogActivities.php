<?php

namespace App\Filament\Resources\LogActivityResource\Pages;

use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\LogActivityResource;

class ListLogActivities extends ListRecords
{
    protected static string $resource = LogActivityResource::class;

    protected static ?string $title = 'List Catatan Aktivitas';

    protected function getActions(): array
    {
        return [];
    }
}
