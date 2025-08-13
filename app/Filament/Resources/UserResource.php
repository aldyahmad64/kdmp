<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;

class UserResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?string $model = User::class;

    protected static ?int $navigationSort = 103;

    protected static ?string $navigationGroup = 'Manajemen Sistem';

    protected static ?string $navigationLabel = 'User';

    protected static ?string $pluralModelLabel = 'User';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $activeNavigationIcon = 'heroicon-m-users';

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
                Forms\Components\TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                    ->autocomplete(false),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->autocomplete(false),
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->revealable()
                    ->dehydrateStateUsing(fn($state) => Hash::make($state))
                    ->dehydrated(fn($state) => filled($state))
                    ->required(fn(string $context): bool => $context === 'create')
                    ->visibleOn(['create', 'edit']),
                Forms\Components\TextInput::make('password_confirmation')
                    ->label('Konfirmasi Password')
                    ->password()
                    ->revealable()
                    ->required(fn(string $context): bool => $context === 'create')
                    ->live()
                    ->dehydrated(false)
                    ->afterStateUpdated(function ($livewire, $component) {
                        $livewire->validateOnly($component->getStatePath());
                    })
                    ->same('password')
                    ->visibleOn(['create', 'edit']),
                Forms\Components\Select::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name', fn(Builder $query) => $query->where('name', '!=', 'SUPER ADMIN'))
                    ->saveRelationshipsUsing(function (Model $record, $state) {
                        $record->roles()->syncWithPivotValues($state, [config('permission.column_names.team_foreign_key') => getPermissionsTeamId()]);
                    })
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->columnSpanFull()
                    ->required()
                    ->hidden(fn($record) => isset($record->id) ? $record->id === 1 : false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar_url')
                    ->label('Avatar')
                    ->circular()
                    ->openUrlInNewTab()
                    ->defaultImageUrl(url('/storage/avatars/default.png')),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
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
                        ->hidden(fn($record) => $record->id === 1 ? (auth()->id() === 1 ? false : true) : false)
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->hidden(fn($record) => $record->id === 1 | $record->id === auth()->id())
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\RestoreAction::make()
                        ->label('Kembalikan')
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
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
        $link = ['index' => Pages\ListUsers::route('/')];
        if (Schema::hasTable('settings')) {
            if (!boolval(Setting::getFormSlide())) {
                $link += [
                    'create' => Pages\CreateUser::route('/create'),
                    'view' => Pages\ViewUsers::route('/{record}'),
                    'edit' => Pages\EditUser::route('/{record}/edit'),
                ];
            }
        }
        return $link;
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
            return static::getEloquentQuery()->count();
        }
        return static::getEloquentQuery()->whereNot('name', 'SUPER ADMIN')->count();
    }
}
