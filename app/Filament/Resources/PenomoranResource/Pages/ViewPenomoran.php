<?php

namespace App\Filament\Resources\PenomoranResource\Pages;

use App\Filament\Resources\PenomoranResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPenomoran extends ViewRecord
{
    protected static string $resource = PenomoranResource::class;

    protected static ?string $title = 'Detail Penomoran';

    protected function getActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->url(static::getResource()::getUrl('index')),
        ];
    }
}
