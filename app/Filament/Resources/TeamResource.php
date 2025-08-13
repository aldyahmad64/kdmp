<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Team;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TeamResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;

class TeamResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?string $model = Team::class;

    protected static bool $isScopedToTenant = false;

    protected static ?int $navigationSort = 103;

    protected static ?string $navigationGroup = 'Manajemen Sistem';

    protected static ?string $navigationLabel = 'Team';

    protected static ?string $pluralModelLabel = 'Team';

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $activeNavigationIcon = 'heroicon-m-user-group';

    public static function canViewAny(): bool
    {
        $tenant = filament()->getTenant();

        return $tenant && $tenant->id === 1 && auth()->user()->canAny('view_team');
    }

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
                Forms\Components\TextInput::make('name')
                    ->label('Nama Team')
                    ->required()
                    ->reactive()
                    ->lazy()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $slug = \Illuminate\Support\Str::slug($state);
                        $set('slug', $slug);
                    }),
                Forms\Components\TextInput::make('slug')
                    ->label('Link / Slug')
                    ->required()
                    ->readOnly()
                    ->extraAttributes([
                        'style' => 'background:#d9d9d9'
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
            ])
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
                        ->hidden(fn($record) => $record->id === 1)
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

        $link = ['index' => Pages\ListTeams::route('/')];
        if (Schema::hasTable('settings')) {
            if (!boolval(Setting::getFormSlide())) {
                $link += [
                    'create' => Pages\CreateTeam::route('/create'),
                    'view' => Pages\ViewTeam::route('/{record}'),
                    'edit' => Pages\EditTeam::route('/{record}/edit'),
                ];
            }
        }
        return $link;
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (!auth()->user()->hasRole('SUPER ADMIN')) {
            $query->where('name', '!=', 'Main');
        }

        return $query;
    }

    public static function getNavigationBadge(): ?string
    {
        if (auth()->user()->hasRole('SUPER ADMIN')) {
            return static::getEloquentQuery()->count();
        }
        return static::getEloquentQuery()->whereNot('id', 1)->count();
    }
}
