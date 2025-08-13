<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\ColorPicker;
use App\Filament\Resources\SettingResource;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class SettingForm extends Page
{
    protected static string $resource = SettingResource::class;

    protected static string $view = 'filament.resources.setting-resource.pages.setting-form';

    protected static ?string $title = 'Setting Umum';

    public array $data = [];

    public function mount()
    {
        $this->data = Setting::all()->mapWithKeys(function ($setting) {
            $value = $setting->value;

            if ($setting->type === 'gambar') {
                $value = $value ? [$value] : null;
            }

            return [$setting->key => $value];
        })->toArray();
    }

    public function save()
    {
        $spa = false;

        foreach ($this->data as $key => $value) {
            $setting = Setting::where('key', $key)->first();
            $val = $value;
            if ($setting && $setting->type === 'gambar') {
                if (!empty($value)) {
                    // Ambil elemen pertama dari array, apapun key-nya
                    $file = is_array($value) ? reset($value) : $value;

                    if ($file instanceof TemporaryUploadedFile) {
                        $path = $file->store('img/web', 'public');
                        $val = $path;

                        // Hapus file temp jika masih ada
                        $tempPath = $file->getRealPath();
                        if (file_exists($tempPath)) {
                            unlink($tempPath);
                        }
                    } else {
                        $val = (string) $file; // pastikan jadi string path
                    }
                } else {
                    $val = 'img/web/logo.png';
                }
            }

            if ($setting && $setting->key === 'web_spa') {
                $spa = $value;
            }

            // Pastikan tidak menyimpan array kalau hanya string
            if (is_array($val) && count($val) === 1) {
                $val = $val[0];
            }

            $setting?->update(['value' => $val]);
        }

        Notification::make()
            ->title('Semua pengaturan berhasil diperbarui!')
            ->success()
            ->send();

        return $this->redirect(SettingResource::getUrl('index'), $navigate = $spa);
    }


    public function form(Form $form): Form
    {
        // Ambil semua setting dan kelompokkan berdasarkan tab
        $groupedSettings = Setting::all()->groupBy('tab');

        return $form->schema([
            \Filament\Forms\Components\Tabs::make('Tabs')
                ->tabs(
                    $groupedSettings->map(function ($settings, $tab) {
                        return \Filament\Forms\Components\Tabs\Tab::make($tab)
                            ->schema(
                                $settings->map(function ($setting) {
                                    return match ($setting->type) {
                                        'text' => TextInput::make("data.{$setting->key}")
                                            ->label($setting->deskripsi)
                                            ->columnSpan(['lg' => 2, 'default' => 6]),

                                        'persen' => TextInput::make("data.{$setting->key}")
                                            ->label($setting->deskripsi)
                                            ->prefix("%")
                                            ->extraAttributes(['style' => 'text-align: right; direction: rtl;'])
                                            ->columnSpan(['lg' => 2, 'default' => 6]),

                                        'boolean' => Select::make("data.{$setting->key}")
                                            ->label($setting->deskripsi)
                                            ->options([
                                                true => 'True',
                                                false => 'False'
                                            ])
                                            ->columnSpan(['lg' => 2, 'default' => 6]),

                                        'gambar' => FileUpload::make("data.{$setting->key}")
                                            ->label($setting->deskripsi)
                                            ->image()
                                            ->imageEditor()
                                            ->imageEditorMode(2)
                                            ->imageEditorAspectRatios([
                                                '16:9',
                                                '4:3',
                                                '1:1',
                                            ])
                                            ->imageCropAspectRatio('1:1')
                                            ->avatar()
                                            ->circleCropper()
                                            ->disk('public')
                                            ->directory('img/web')
                                            ->columnSpan(['default' => 6])
                                            ->extraAttributes(['style' => 'margin-left:auto; margin-right:auto; display:block;']),

                                        'warna' => ColorPicker::make("data.{$setting->key}")
                                            ->label($setting->deskripsi)
                                            ->rgb()
                                            ->columnSpan(['lg' => 2, 'md' => 3, 'default' => 6]),

                                        default => TextInput::make("data.{$setting->key}")
                                            ->label($setting->deskripsi),
                                    };
                                })->toArray()
                            );
                    })->values()->toArray() // Convert collection to plain array
                )
                ->columns(6)
        ]);
    }

}
