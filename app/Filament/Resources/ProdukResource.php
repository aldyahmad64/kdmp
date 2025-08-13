<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Produk;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProdukResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProdukResource extends Resource
{
    protected static ?string $model = Produk::class;

    protected static bool $isScopedToTenant = true;

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $activeNavigationIcon = 'heroicon-m-cube';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'restore',
            'restore_any',
            'force_delete',
            'force_delete_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                \Filament\Forms\Components\Tabs::make('Tabs')
                    ->tabs([
                        \Filament\Forms\Components\Tabs\Tab::make('Data Produk')
                            ->schema([
                                Forms\Components\Select::make('kategori_id')
                                    ->label('Kategori')
                                    ->placeholder('Pilih Kategori...')
                                    ->relationship('kategori', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(['lg' => 2, 'default' => 6]),
                                Forms\Components\TextInput::make('sku')
                                    ->label('SKU')
                                    ->columnSpan(['lg' => 2, 'default' => 6]),
                                Forms\Components\Select::make('default_unit_id')
                                    ->label('Default Satuan')
                                    ->placeholder('Pilih Satuan...')
                                    ->relationship('defaultUnit', 'nama')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->columnSpan(['lg' => 2, 'default' => 6]),
                                Forms\Components\TextInput::make('nama')
                                    ->label('Nama Produk')
                                    ->required()
                                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                                    ->columnSpan(['lg' => 6, 'default' => 6]),
                                Forms\Components\FileUpload::make('gambar')
                                    ->label('Gambar Produk')
                                    ->default('img/web/logo.png')
                                    ->avatar()
                                    ->openable()
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorMode(2)
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->circleCropper()
                                    ->directory('img/produk')
                                    ->required()
                                    ->required()->extraAttributes(['style' => 'margin-left:auto; margin-right:auto; display:block;'])
                                    ->columnSpan(['lg' => 2, 'default' => 6]),
                                Forms\Components\Textarea::make('deskripsi')
                                    ->label('Deskripsi')
                                    ->rows(5)
                                    ->columnSpan(['lg' => 4, 'default' => 6]),
                            ])
                            ->columns(6),
                        \Filament\Forms\Components\Tabs\Tab::make('Satuan Produk')
                            ->schema([
                                \Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater::make('units')
                                    ->label('')
                                    ->relationship('units')
                                    ->schema([
                                        Forms\Components\Select::make('unit_id')
                                            ->label('Satuan')
                                            ->placeholder('Pilih Satuan...')
                                            ->relationship('unit', 'nama')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        \Filament\Forms\Components\TextInput::make('konversi')
                                            ->label('Nilai Pengali')
                                            ->prefix('X'),
                                    ])
                                    ->columns(2)
                                    ->reorderable()
                                    ->addActionLabel('Tambah Komponen')
                                    ->columnSpanFull(),
                            ]),
                        \Filament\Forms\Components\Tabs\Tab::make('Harga Produk')
                            ->schema([
                                \Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater::make('hargas')
                                    ->label('')
                                    ->relationship('hargas')
                                    ->schema([
                                        Forms\Components\Select::make('tipe_harga_id')
                                            ->label('Jenis Harga')
                                            ->placeholder('Pilih Jenis...')
                                            ->relationship('tipeHarga', 'nama')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Forms\Components\Select::make('unit_id')
                                            ->label('Satuan')
                                            ->placeholder('Pilih Satuan...')
                                            ->relationship('unit', 'nama')
                                            ->searchable()
                                            ->preload()
                                            ->required(),
                                        Forms\Components\TextInput::make('harga')
                                            ->label('Harga')
                                            ->prefix('Rp')
                                            ->extraAlpineAttributes([
                                                'x-on:input' => "if (\$el.value) \$el.value = \$el.value.replace(/[^0-9]/g, '')"
                                            ])
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->reorderable()
                                    ->addActionLabel('Tambah Komponen')
                                    ->columnSpanFull(),
                            ])
                    ])
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label('SKU')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('defaultUnit.nama')
                    ->label('Default Satuan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->poll(15)
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->label('Lihat')
                        ->modalWidth(MaxWidth::Full)
                        ->slideOver(),
                    Tables\Actions\EditAction::make()
                        ->label('Ubah')
                        ->modalWidth(MaxWidth::Full)
                        ->slideOver()
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\RestoreAction::make()
                        ->label('Kembalikan')
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->successRedirectUrl(self::getUrl()),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\ForceDeleteBulkAction::make()
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\RestoreBulkAction::make()
                        ->successRedirectUrl(self::getUrl()),
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

        $link = ['index' => Pages\ListProduks::route('/')];
        if (Schema::hasTable('settings')) {
            if (!boolval(Setting::getFormSlide())) {
                $link += [
                    'create' => Pages\CreateProduk::route('/create'),
                    'view' => Pages\ViewProduk::route('/{record}'),
                    'edit' => Pages\EditProduk::route('/{record}/edit'),
                ];
            }
        }
        return $link;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getEloquentQuery()->count();
    }
}
