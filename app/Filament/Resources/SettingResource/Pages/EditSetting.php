<?php

namespace App\Filament\Resources\SettingResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\SettingResource;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected static ?string $title = 'Ubah Setting';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

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

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['value'] = match ($data['type']) {
            'text' => $data['value_text'] ?? '',
            'richtext' => $data['value_richtext'] ?? '',
            'gambar' => $data['value_gambar'] ?? '',
            'warna' => $data['value_warna'] ?? '',
            'boolean' => !empty($data['value_boolean']) ? '1' : '0',
            default => '',
        };
        unset($data['value_text']);
        unset($data['value_richtext']);
        unset($data['value_gambar']);
        unset($data['value_warna']);
        unset($data['value_boolean']);
        return $data;
    }
}
