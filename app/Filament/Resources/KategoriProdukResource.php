<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\KategoriProduk;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\KategoriProdukResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;

class KategoriProdukResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?string $model = KategoriProduk::class;

    protected static bool $isScopedToTenant = true;

    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Kategori Produk';

    protected static ?string $pluralModelLabel = 'Kategori Produk';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $activeNavigationIcon = 'heroicon-m-tag';

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
                    ->label('Nama Kategori')
                    ->required()
                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                    ->columnSpanFull(),
            ])
            ->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama')
                    ->label('Kategori Produk')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->sortable()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d-m-Y H:I:s'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->sortable()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d-m-Y H:I:s'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->sortable()
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d-m-Y H:I:s'))
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
        $link = ['index' => Pages\ListKategoriProduks::route('/')];
        if (Schema::hasTable('settings')) {
            if (!boolval(Setting::getFormSlide())) {
                $link += [
                    'create' => Pages\CreateKategoriProduk::route('/create'),
                    'view' => Pages\ViewKategoriProduk::route('/{record}'),
                    'edit' => Pages\EditKategoriProduk::route('/{record}/edit'),
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
