<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use App\Filament\Resources\SettingResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;

class SettingResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?string $model = Setting::class;

    protected static bool $isScopedToTenant = true;

    protected static ?int $navigationSort = 104;

    protected static ?string $navigationGroup = 'Manajemen Sistem';

    protected static ?string $navigationLabel = 'Setting Umum';

    protected static ?string $pluralModelLabel = 'Setting Umum';

    protected static ?string $navigationIcon = 'heroicon-o-cog-8-tooth';

    protected static ?string $activeNavigationIcon = 'heroicon-m-cog-8-tooth';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('key')
                    ->label('Title')
                    ->required()
                    ->disabled(fn($record) => $record?->is_default)
                    ->dehydrated(true)
                    ->columnSpan(['md' => 6, 'lg' => 5]),
                Forms\Components\Select::make('type')
                    ->label('Tipe Data')
                    ->options([
                        'text' => 'Text',
                        'richtext' => 'Rich Text',
                        'gambar' => 'Gambar',
                        'boolean' => 'Boolean',
                        'warna' => 'Warna'
                    ])
                    ->default('text')
                    ->live()
                    ->disabled(fn($record) => $record?->is_default)
                    ->dehydrated(true)
                    ->columnSpan(['md' => 6, 'lg' => 1]),
                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi')
                    ->required()
                    ->disabled(fn($record) => $record?->is_default && $record?->type !== 'richtext')
                    ->dehydrated(true)
                    ->columnSpanFull(),
                \FilamentTiptapEditor\TiptapEditor::make('value_richtext')
                    ->label('Konten')
                    ->required()
                    ->visible(fn($get) => $get('type') === 'richtext')
                    ->profile('bloger')
                    ->disk('public') // opsional, override dari config
                    ->directory('tiptap-images') // tempat simpan gambar
                    ->acceptedFileTypes(['image/*']) // hanya gambar
                    ->maxSize(5120) // 5 MB
                    ->output(\FilamentTiptapEditor\Enums\TiptapOutput::Html) // hasil simpan dalam HTML
                    ->columnSpanFull()
                    ->extraInputAttributes(['style' => 'min-height: 12rem;'])
                    ->maxContentWidth('screen'),
                Forms\Components\Textarea::make('value_text')
                    ->label('Isi Text')
                    ->required()
                    ->visible(fn($get) => $get('type') === 'text')
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('value_gambar')
                    ->label('Upload Gambar')
                    ->image()
                    ->imageEditor()
                    ->imageEditorMode(2)
                    ->imageEditorAspectRatios([
                        '16:9',
                        '4:3',
                        '1:1',
                    ])
                    ->disk('public')
                    ->directory('img/web')
                    ->required()
                    ->visible(fn($get) => $get('type') === 'gambar')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('value_boolean')
                    ->label('Nilai Boolean')
                    ->required()
                    ->visible(fn($get) => $get('type') === 'boolean')
                    ->columnSpanFull(),
                Forms\Components\ColorPicker::make('value_warna')
                    ->rgb()
                    ->required()
                    ->visible(fn($get) => $get('type') === 'warna')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('deskripsi')
                    ->label('Deskripsi'),
                Tables\Columns\TextColumn::make('key')
                    ->label('Nama Key'),
                Tables\Columns\ViewColumn::make('value')
                    ->label('Nilai Isi')
                    ->view('filament.admin.setting')
                    ->viewData(fn($record) => [
                        'record' => $record,
                    ]),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir diubah')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn($state) => $state->diffForHumans()),
            ])
            ->paginated(false)
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Lihat')
                        ->modalWidth(MaxWidth::ScreenLarge)
                        ->slideOver(),
                    Tables\Actions\EditAction::make()
                        ->label('Ubah')
                        ->modalWidth(MaxWidth::ScreenLarge)
                        ->slideOver(),
                ]),
            ])
            ->actionsColumnLabel('Aksi')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\SettingForm::route('/'),
            // 'index' => Pages\ListSettings::route('/'),
            // 'create' => Pages\CreateSetting::route('/create'),
            // 'view' => Pages\ViewSetting::route('/{record}'),
            // 'edit' => Pages\EditSetting::route('/{record}/edit'),
        ];
    }
}
