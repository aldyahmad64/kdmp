<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Unit;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UnitResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UnitResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;

class UnitResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?string $model = Unit::class;

    protected static bool $isScopedToTenant = true;

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Satuan Produk';

    protected static ?string $pluralModelLabel = 'Satuan Produk';

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $activeNavigationIcon = 'heroicon-m-square-3-stack-3d';

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
                Forms\Components\TextInput::make('nama')
                    ->label('Satuan Produk')
                    ->required()
                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('level')
                    ->label('Level')
                    ->helperText('Misalnya level = 1 untuk PCS (satuan terkecil), level = 2 untuk Pack, level = 3 untuk Dus.')
                    ->required()
                    ->numeric()
                    ->columnSpanFull(),
            ])
            ->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Satuan Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('level')
                    ->label('Level')
                    ->numeric()
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
        $link = ['index' => Pages\ListUnits::route('/')];
        if (Schema::hasTable('settings')) {
            if (!boolval(Setting::getFormSlide())) {
                $link += [
                    'create' => Pages\CreateUnit::route('/create'),
                    'view' => Pages\ViewUnit::route('/{record}'),
                    'edit' => Pages\EditUnit::route('/{record}/edit'),
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
