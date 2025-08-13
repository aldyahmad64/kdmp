<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Arrayable;
use App\Filament\Resources\RoleResource\Pages;
use BezhanSalleh\FilamentShield\Support\Utils;
use BezhanSalleh\FilamentShield\Forms\ShieldSelectAllToggle;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\RoleResource\Pages\HasShieldFormComponents;

class RoleResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?int $navigationSort = 102;

    protected static ?string $navigationGroup = 'Manajemen Sistem';

    protected static ?string $navigationLabel = 'Role';

    protected static ?string $pluralModelLabel = 'Role';

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
                Forms\Components\Grid::make()
                    ->schema([
                        Forms\Components\Section::make()
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label('Deskripsi')
                                    ->required()
                                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                                    ->autocomplete(false)
                                    ->maxLength(255),
                                Forms\Components\Select::make('guard_name')
                                    ->label('Type')
                                    ->default(Utils::getFilamentAuthGuard())
                                    ->options([
                                        'web' => 'WEB',
                                        'api' => 'API'
                                    ])
                                    ->disableOptionWhen(function ($value) {
                                        return Str::contains($value, 'api');
                                    }),
                                Forms\Components\Select::make(config('permission.column_names.team_foreign_key'))
                                    ->label(__('filament-shield::filament-shield.field.team'))
                                    ->placeholder(__('filament-shield::filament-shield.field.team.placeholder'))
                                    ->default([Filament::getTenant()?->id])
                                    ->options(fn(): Arrayable => Utils::getTenantModel() ? Utils::getTenantModel()::pluck('name', 'id') : collect())
                                    ->hidden(fn(): bool => !(static::shield()->isCentralApp() && Utils::isTenancyEnabled()))
                                    ->dehydrated(fn(): bool => !(static::shield()->isCentralApp() && Utils::isTenancyEnabled())),
                                ShieldSelectAllToggle::make('select_all')
                                    ->onIcon('heroicon-s-shield-check')
                                    ->offIcon('heroicon-s-shield-exclamation')
                                    ->label(__('filament-shield::filament-shield.field.select_all.name'))
                                    ->helperText(fn(): HtmlString => new HtmlString(__('filament-shield::filament-shield.field.select_all.message')))
                                    ->dehydrated(fn(bool $state): bool => $state),
                            ])
                            ->columns([
                                'sm' => 2,
                                'lg' => 3,
                            ]),
                    ]),
                static::getShieldFormComponents(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->weight('font-medium')
                    ->label('Deskripsi')
                    ->formatStateUsing(fn($state): string => Str::headline($state))
                    ->searchable(),
                Tables\Columns\TextColumn::make('guard_name')
                    ->badge()
                    ->color('warning')
                    ->label('Type')
                    ->icon(fn($state) => $state === 'web' ? 'heroicon-o-globe-alt' : 'heroicon-o-fire'),
                Tables\Columns\TextColumn::make('team.name')
                    ->default('Global')
                    ->badge()
                    ->color(fn(mixed $state): string => str($state)->contains('Global') ? 'gray' : 'primary')
                    ->label(__('filament-shield::filament-shield.column.team'))
                    ->searchable()
                    ->visible(fn(): bool => static::shield()->isCentralApp() && Utils::isTenancyEnabled()),
                Tables\Columns\TextColumn::make('permissions_count')
                    ->badge()
                    ->label(__('filament-shield::filament-shield.column.permissions'))
                    ->counts('permissions')
                    ->colors(['success']),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->formatStateUsing(fn($state) => $state->diffForHumans())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Dirubah')
                    ->formatStateUsing(fn($state) => $state->diffForHumans())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                // Tables\Actions\DeleteBulkAction::make(),
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
        $link = [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'view' => Pages\ViewRole::route('/{record}'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
        return $link;
    }

    public static function getCluster(): ?string
    {
        return Utils::getResourceCluster() ?? static::$cluster;
    }

    public static function getModel(): string
    {
        return Utils::getRoleModel();
    }

    // public static function getModelLabel(): string
    // {
    //     return __('filament-shield::filament-shield.resource.label.role');
    // }

    // public static function getPluralModelLabel(): string
    // {
    //     return __('filament-shield::filament-shield.resource.label.roles');
    // }

    public static function shouldRegisterNavigation(): bool
    {
        return Utils::isResourceNavigationRegistered();
    }

    // public static function getNavigationGroup(): ?string
    // {
    //     return Utils::isResourceNavigationGroupEnabled()
    //         ? __('filament-shield::filament-shield.nav.group')
    //         : '';
    // }

    // public static function getNavigationLabel(): string
    // {
    //     return __('filament-shield::filament-shield.nav.role.label');
    // }

    public static function getNavigationIcon(): string
    {
        return __('filament-shield::filament-shield.nav.role.icon');
    }

    public static function getNavigationSort(): ?int
    {
        return Utils::getResourceNavigationSort();
    }

    public static function getSlug(): string
    {
        return Utils::getResourceSlug();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->hasRole('SUPER ADMIN')) {
            $query->where('name', '!=', 'SUPER ADMIN');
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {
            return Utils::isResourceNavigationBadgeEnabled()
                ? strval(static::getEloquentQuery()->count())
                : null;
        }
        return Utils::isResourceNavigationBadgeEnabled()
            ? strval(static::getEloquentQuery()->whereNot('name', 'SUPER ADMIN')->count())
            : null;
    }

    public static function isScopedToTenant(): bool
    {
        return Utils::isScopedToTenant();
    }

    public static function canGloballySearch(): bool
    {
        return Utils::isResourceGloballySearchable() && count(static::getGloballySearchableAttributes()) && static::canViewAny();
    }
}
