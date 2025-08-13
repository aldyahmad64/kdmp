<?php

namespace App\Filament\Resources\PenomoranResource\Pages;

use Filament\Actions;
use Filament\Support\Enums\MaxWidth;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PenomoranResource;

class ListPenomorans extends ListRecords
{
    protected static string $resource = PenomoranResource::class;

    protected static ?string $title = 'List Penomoran';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Penomoran Baru')
                ->modalWidth(MaxWidth::Full)
                ->slideOver()
                ->createAnother(false),
        ];
    }
}
