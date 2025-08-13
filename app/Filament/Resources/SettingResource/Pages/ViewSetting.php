<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\SettingResource;

class ViewSetting extends ViewRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $title = 'Detail Setting';

    protected function getActions(): array
    {
        return [
            Actions\Action::make('kembali')
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        match ($data['type']) {
            'text' => $data['value_text'] = $data['value'],
            'richtext' => $data['value_richtext'] = $data['value'],
            'gambar' => $data['value_gambar'] = $data['value'],
            'warna' => $data['value_warna'] = $data['value'],
            'boolean' => $data['value_boolean'] = $data['value'] === '1',
        };

        return $data;
    }
}
