<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\SettingResource;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected static bool $canCreateAnother = false;

    protected static ?string $title = 'Buat Setting';

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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['value'] = match ($data['type']) {
            'text' => $data['value_text'] ?? '',
            'richtext' => $data['value_richtext'] ?? '',
            'gambar' => $data['value_gambar'] ?? '',
            'warna' => $data['value_warna'] ?? '',
            'boolean' => !empty($data['value_boolean']) ? '1' : '0',
            default => '',
        };
        $data['is_default'] = false;
        unset($data['value_text']);
        unset($data['value_richtext']);
        unset($data['value_gambar']);
        unset($data['value_warna']);
        unset($data['value_boolean']);

        return $data;
    }
}
