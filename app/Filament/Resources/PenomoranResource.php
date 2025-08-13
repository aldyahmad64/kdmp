<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Form;
use App\Models\Penomoran;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\PenomoranResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PenomoranResource\RelationManagers;

class PenomoranResource extends Resource
{
    protected static ?string $model = Penomoran::class;

    protected static bool $isScopedToTenant = true;

    protected static ?int $navigationSort = 105;

    protected static ?string $navigationGroup = 'Manajemen Sistem';

    protected static ?string $navigationLabel = 'Penomoran';

    protected static ?string $pluralModelLabel = 'Penomoran';

    protected static ?string $navigationIcon = 'heroicon-o-numbered-list';

    protected static ?string $activeNavigationIcon = 'heroicon-m-numbered-list';

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
                \Filament\Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Nama Format')
                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                    ->columnSpanFull(),

                \Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater::make('components')
                    ->label('Komponen Nomor')
                    ->schema([
                        \Filament\Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'static' => 'Static Text',
                                'counter' => 'Counter',
                                'monthRoman' => 'Bulan (Romawi)',
                                'date' => 'Tanggal',
                                'month' => 'Bulan',
                                'year' => 'Tahun',
                            ])
                            ->required()
                            ->reactive(),

                        \Filament\Forms\Components\TextInput::make('value')
                            ->label('Nilai')
                            ->prefix('Nilai')
                            ->disabled(fn($get) => $get('type') === 'monthRoman' | $get('type') === 'date' | $get('type') === 'month' | $get('type') === 'year')
                            ->visible(fn($get) => $get('type') !== 'counter'),

                        \Filament\Forms\Components\TextInput::make('padding')
                            ->label('Panjang Digit')
                            ->numeric()
                            ->default(3)
                            ->prefix('Panjang Digit')
                            ->visible(fn($get) => $get('type') === 'counter'),
                    ])
                    ->columns(2)
                    ->reorderable()
                    ->addActionLabel('Tambah Komponen')
                    ->columnSpanFull(),

                \Filament\Forms\Components\TextInput::make('counter')
                    ->numeric()
                    ->default(1)
                    ->label('Nomor Selanjutnya')
                    ->required()
                    ->columnSpanFull(),

                \Filament\Forms\Components\Hidden::make('last_reset')
                    ->default(null),

                \Filament\Forms\Components\Placeholder::make('preview')
                    ->label('Preview Nomor Surat')
                    ->content(function ($get) {
                        $fake = new Penomoran([
                            'components' => $get('components') ?? [],
                            'counter' => $get('counter') ?? 1,
                        ]);

                        return \App\Services\PenomoranServices::preview($fake);
                    })
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Deskripsi')
                    ->sortable()
                    ->extraAttributes(['style' => 'text-transform: capitalize'])
                    ->searchable(),
                Tables\Columns\TextColumn::make('components')
                    ->label('Format')
                    ->formatStateUsing(function ($record) {
                        $fake = new Penomoran([
                            'components' => $record->components ?? [],
                            'counter' => $record->counter ?? 1,
                        ]);
                        return \App\Services\PenomoranServices::preview($fake);
                    })
                    ->sortable()
                    ->searchable(),
            ])
            ->poll(15)
            ->filters([
                //
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
                        ->hidden(fn($record) => $record->id === 1 ? (auth()->id() === 1 ? false : true) : false)
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->hidden(fn($record) => $record->id === 1)
                        ->successRedirectUrl(self::getUrl()),
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
        $link = ['index' => Pages\ListPenomorans::route('/')];
        if (Schema::hasTable('settings')) {
            if (!boolval(Setting::getFormSlide())) {
                $link += [
                    'create' => Pages\CreatePenomoran::route('/create'),
                    'view' => Pages\ViewPenomoran::route('/{record}'),
                    'edit' => Pages\EditPenomoran::route('/{record}/edit'),
                ];
            }
        }
        return $link;
    }
}
