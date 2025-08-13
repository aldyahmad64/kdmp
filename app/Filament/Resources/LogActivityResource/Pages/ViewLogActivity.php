<?php

namespace App\Filament\Resources\LogActivityResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\LogActivityResource;

class ViewLogActivity extends ViewRecord
{
    protected static string $resource = LogActivityResource::class;

    protected static ?string $title = 'Detail Catatan Aktivitas';

    protected function getActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
