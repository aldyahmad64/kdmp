<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Tables;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\LogActivity;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LogActivityResource\Pages;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;

class LogActivityResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?string $model = LogActivity::class;

    protected static bool $isScopedToTenant = true;

    protected static ?int $navigationSort = 110;

    protected static ?string $navigationGroup = 'Informasi Sistem';

    protected static ?string $navigationLabel = 'Catatan Aktivitas';

    protected static ?string $pluralModelLabel = 'Catatan Aktivitas';

    protected static ?string $navigationIcon = 'heroicon-o-information-circle';

    protected static ?string $activeNavigationIcon = 'heroicon-m-information-circle';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
        ];
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('user_id')
                    ->label('User')
                    ->formatStateUsing(fn($record) => $record->user->name)
                    ->columnSpan(['md' => 2, 'default' => 6]),
                TextInput::make('created_at')
                    ->label('Tanggal')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d-m-Y H:i:s'))
                    ->columnSpan(['md' => 2, 'default' => 6]),
                TextInput::make('event')
                    ->label('Aksi')
                    ->columnSpan(['md' => 2, 'default' => 6]),
                TextInput::make('deskripsi')
                    ->label('Deskripsi')
                    ->columnSpan(['md' => 6, 'default' => 6]),
                ViewField::make('old_values')
                    ->label('Data Lama')
                    ->view('forms.components.log')
                    ->columnSpan(['default' => 6]),
                ViewField::make('new_values')
                    ->label('Data Baru')
                    ->view('forms.components.log')
                    ->columnSpan(['default' => 6]),
            ])
            ->columns(6);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable(),
                TextColumn::make('deskripsi')
                    ->label('Deskripsi'),
                TextColumn::make('new_values')
                    ->label('Data Baru')
                    ->formatStateUsing(function ($record, $state) {
                        if ($state === 1 || $state === '1') {
                            return 'true';
                        }
                        if ($state === 0 || $state === '0') {
                            return 'false';
                        }
                        return Str::limit($state, 40);
                    }),
                TextColumn::make('event')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'warning',
                        'deleted' => 'danger',
                        'rollback' => 'gray',
                        default => 'secondary',
                    }),
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->formatStateUsing(fn($state) => Carbon::parse($state)->format('d-m-Y H:I:s'))
                    ->sortable(),
            ])
            ->poll(15)
            ->defaultPaginationPageOption(5)
            ->paginated(true)
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('tanggal')
                    ->form([
                        DatePicker::make('dari')->label('Dari Tanggal')->default(now())->columnSpan(2),
                        DatePicker::make('sampai')->label('Sampai Tanggal')->default(now())->columnSpan(2),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['dari'], fn($q) => $q->whereDate('created_at', '>=', $data['dari']))
                            ->when($data['sampai'], fn($q) => $q->whereDate('created_at', '<=', $data['sampai']));
                    })
                    ->columns(4)
                    ->columnSpanFull(),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(4)
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat')
                    ->modalHeading('Detail Catatan Aktivitas')
                    ->modalWidth(MaxWidth::Full)
                    ->slideOver(),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        $link = ['index' => Pages\ListLogActivities::route('/')];
        if (Schema::hasTable('settings')) {
            if (!boolval(Setting::getFormSlide())) {
                $link += [
                    'view' => Pages\ViewLogActivity::route('/{record}'),
                ];
            }
        }
        return $link;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('user');
    }
}
