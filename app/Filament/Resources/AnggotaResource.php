<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Anggota;
use App\Models\Setting;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\AnggotaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AnggotaResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use BezhanSalleh\FilamentShield\Traits\HasShieldFormComponents;

class AnggotaResource extends Resource implements HasShieldPermissions
{
    use HasShieldFormComponents;

    protected static ?string $model = Anggota::class;

    protected static bool $isScopedToTenant = true;

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Anggota';

    protected static ?string $pluralModelLabel = 'Anggota';

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $activeNavigationIcon = 'heroicon-m-user';

    public static function shouldRegisterNavigation(): bool
    {
        $tenant = filament()->getTenant();
        $role = \App\Models\Role::where('team_id', '=', $tenant->id)
            ->where('name', '=', 'anggota')
            ->first();
        return $tenant && $role?->team_id == $tenant->id;
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
                        \Filament\Forms\Components\Tabs\Tab::make('Biodata')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('kode')
                                    ->label('Kode Anggota')
                                    ->default('AUTO')
                                    ->placeholder('Kode Anggota...')
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->reactive()
                                    ->readOnly(fn($get) => $get('kode') === 'AUTO')
                                    ->readOnlyOn(['view', 'edit'])
                                    ->autocomplete(false)
                                    ->suffixActions(
                                        [
                                            \Filament\Forms\Components\Actions\Action::make('AUTO')
                                                ->label('')
                                                ->icon('heroicon-m-bolt')
                                                ->disabled(function () {
                                                    $livewire = \Livewire\Livewire::current();
                                                    return $livewire instanceof \Filament\Resources\Pages\EditRecord
                                                        || $livewire instanceof \Filament\Resources\Pages\ViewRecord;
                                                })
                                                ->action(fn($set, $get) => $get('kode') === 'AUTO' ? $set('kode', '') : $set('kode', 'AUTO'))
                                        ]
                                    )
                                    ->extraInputAttributes(['maxlength' => 16])
                                    ->columnSpan(['lg' => 2, 'default' => 8]),
                                \Filament\Forms\Components\TextInput::make('nik')
                                    ->label('Nomor Identitas')
                                    ->placeholder('Nomor identitas...')
                                    ->unique(ignoreRecord: true)
                                    ->required()
                                    ->autocomplete(false)
                                    ->extraAlpineAttributes([
                                        'x-on:input' => "if (\$el.value) \$el.value = \$el.value.replace(/[^0-9]/g, '')"
                                    ])
                                    ->extraInputAttributes(['maxlength' => 16])
                                    ->columnSpan(['lg' => 3, 'default' => 8]),
                                \Filament\Forms\Components\TextInput::make('npwp')
                                    ->label('Nomor Pajak')
                                    ->placeholder('Boleh dikosongkan...')
                                    ->unique(ignoreRecord: true)
                                    ->autocomplete(false)
                                    ->extraAlpineAttributes([
                                        'x-on:input' => "if (\$el.value) \$el.value = \$el.value.replace(/[^0-9]/g, '')"
                                    ])
                                    ->extraInputAttributes(['maxlength' => 16])
                                    ->columnSpan(['lg' => 3, 'default' => 8]),
                                \Filament\Forms\Components\TextInput::make('nama')
                                    ->label('Nama Lengkap')
                                    ->placeholder('Nama lengkap...')
                                    ->required()
                                    ->autocomplete(false)
                                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                                    ->columnSpan(['lg' => 8, 'default' => 8]),
                                \Filament\Forms\Components\Select::make('jenis_kelamin')
                                    ->label('Jenis Kelamin')
                                    ->placeholder('Pilih')
                                    ->options(['L' => 'Laki-laki', 'P' => 'Perempuan'])
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(['lg' => 2, 'default' => 8]),
                                \Filament\Forms\Components\TextInput::make('tempat_lahir')
                                    ->label('Tempat Lahir')
                                    ->placeholder('Tempat lahir...')
                                    ->required()
                                    ->autocomplete(false)
                                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                                    ->columnSpan(['lg' => 4, 'default' => 8]),
                                \Filament\Forms\Components\DatePicker::make('tanggal_lahir')
                                    ->label('Tangal Lahir')
                                    ->placeholder('dd/mm/yyyy')
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->required()
                                    ->columnSpan(['lg' => 2, 'default' => 8]),
                                \Filament\Forms\Components\Select::make('status_pernikahan')
                                    ->label('Status Pernikahan')
                                    ->placeholder('Pilih')
                                    ->options(['LAJANG' => 'Lajang', 'MENIKAH' => 'Menikah', 'CERAI HIDUP' => 'Cerai Hidup', 'CERAI MATI' => 'Cerai Mati'])
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(['lg' => 3, 'default' => 8]),
                                \Filament\Forms\Components\TextInput::make('nomor_hp')
                                    ->label('Nomor Handphone')
                                    ->placeholder('Nomor handphone...')
                                    ->autocomplete(false)
                                    ->extraAlpineAttributes([
                                        'x-on:input' => "if (\$el.value) \$el.value = \$el.value.replace(/[^0-9]/g, '')"
                                    ])
                                    ->extraInputAttributes(['maxlength' => 13])
                                    ->columnSpan(['lg' => 5, 'default' => 8]),
                                \Filament\Forms\Components\Textarea::make('alamat')
                                    ->label('Alamat Lengkap')
                                    ->placeholder('Alamat lengkap...')
                                    ->rows(3)
                                    ->required()
                                    ->autocomplete(false)
                                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                                    ->columnSpan(['lg' => 8, 'default' => 8]),
                                \Filament\Forms\Components\FileUpload::make('foto')
                                    ->label('Foto')
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
                                    ->directory('img/anggota/foto')
                                    ->required()->extraAttributes(['style' => 'margin-left:auto; margin-right:auto; display:block;'])
                                    ->columnSpan(['lg' => 2, 'default' => 8]),
                                \Filament\Forms\Components\FileUpload::make('lampiran')
                                    ->label('Lampiran')
                                    ->openable()
                                    ->image()
                                    ->imageEditor()
                                    ->imageEditorMode(2)
                                    ->imageEditorAspectRatios([
                                        '16:9',
                                        '4:3',
                                        '1:1',
                                    ])
                                    ->multiple()
                                    ->panelLayout('grid')
                                    ->directory('img/anggota/lampiran')
                                    ->columnSpan(['lg' => 6, 'default' => 8]),
                            ])
                            ->columns(8)
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Tabs\Tab::make('Pekerjaan')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('pekerjaan.deskripsi')
                                    ->label('Deskripsi Pekerjaan')
                                    ->placeholder('Deskripsi pekerjaan...')
                                    ->autocomplete(false)
                                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                                    ->columnSpanFull(),
                                \Filament\Forms\Components\Textarea::make('pekerjaan.alamat')
                                    ->label('Alamat Lengkap Pekerjaan')
                                    ->placeholder('Alamat lengkap pekerjaan...')
                                    ->rows(3)
                                    ->autocomplete(false)
                                    ->extraInputAttributes(['oninput' => 'this.value = this.value.toUpperCase()'])
                                    ->columnSpanFull(),
                                \Filament\Forms\Components\Radio::make('pekerjaan.penghasilan')
                                    ->label('Penghasilan Perbulan')
                                    ->options([
                                        'desil-1' => '0 Sampai 1,5 Juta',
                                        'desil-2' => '1,5 Sampai 2,2 Juta',
                                        'desil-3' => '2,2 Sampai 2,8 Juta',
                                        'desil-4' => '2,8 Sampai 3,4 Juta',
                                        'desil-5' => '3,4 Sampai 4,1 Juta',
                                        'desil-6' => '4,1 Sampai 5 Juta',
                                        'desil-7' => '5 Sampai 6,2 Juta',
                                        'desil-8' => '6,2 Sampai 8,1 Juta',
                                        'desil-9' => '8,1 Sampai 12,7',
                                        'desil-10' => 'Lebih Dari 12,7',
                                    ])
                                    ->descriptions([
                                        'desil-1' => 'Penghasilan 0 - 1,5 Juta',
                                        'desil-2' => 'Penghasilan 1,5 - 2,2 Juta',
                                        'desil-3' => 'Penghasilan 2,2 - 2,8 Juta',
                                        'desil-4' => 'Penghasilan 2,8 - 3,4 Juta',
                                        'desil-5' => 'Penghasilan 3,4 - 4,1 Juta',
                                        'desil-6' => 'Penghasilan 4,1 - 5 Juta',
                                        'desil-7' => 'Penghasilan 5 - 6,2 Juta',
                                        'desil-8' => 'Penghasilan 6,2 - 8,1 Juta',
                                        'desil-9' => 'Penghasilan 8,1 - 12,7',
                                        'desil-10' => 'Penghasilan Lebih Dari 12,7',
                                    ])
                                    ->columns(['lg' => 5, 'default' => 2])
                                    ->gridDirection('row')
                                    ->columnSpanFull(),
                            ])
                            ->columns(6)
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Tabs\Tab::make('Akun')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('user.email')
                                    ->label('Email')
                                    ->formatStateUsing(fn($record) => !$record ? '' : \App\Models\User::withTrashed()->find($record->user_id)->email)
                                    ->prefixIcon('heroicon-o-envelope')
                                    ->suffixAction(
                                        \Filament\Forms\Components\Actions\Action::make('generateEmail')
                                            ->icon('heroicon-o-sparkles')
                                            ->tooltip('Buat Email Otomatis')
                                            ->action(function ($set, $get) {
                                                $set('user.email', $get('nik') . '.' . \Illuminate\Support\Str::slug(filament()->getTenant()->name, '') . '@' . str_replace(['https://', 'http://'], '', env('APP_URL')));
                                            })
                                    )
                                    ->email()
                                    ->rules([
                                        fn(): \Closure => function (string $attribute, $value, \Closure $fail) {
                                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                                                $fail('Email tidak valid');
                                            }
                                        },
                                    ])
                                    ->readOnlyOn('edit')
                                    ->required()
                                    ->unique(
                                        table: 'users',
                                        column: 'email',
                                        ignoreRecord: true,
                                        ignorable: fn($record) => \App\Models\User::withTrashed()->find($record?->user_id)
                                    )
                                    ->autocomplete(false)
                                    ->columnSpanFull(),
                                \Filament\Forms\Components\TextInput::make('user.password')
                                    ->label('Password')
                                    ->default('123456')
                                    ->password()
                                    ->revealable()
                                    ->dehydrateStateUsing(fn($state) => \Illuminate\Support\Facades\Hash::make($state))
                                    ->dehydrated(fn($state) => filled($state))
                                    ->required(fn(string $context): bool => $context === 'create')
                                    ->visibleOn(['create', 'edit'])
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                        \Filament\Forms\Components\Tabs\Tab::make('Saldo')
                            ->schema([
                                \Filament\Forms\Components\TextInput::make('saldo.saldo_pokok')
                                    ->label('Saldo Tabungan Pokok')
                                    ->prefix('Rp .')
                                    ->default(0)
                                    ->readOnly()
                                    ->columnSpan(['lg' => 2, 'default' => 6])
                                    ->extraAttributes([
                                        'style' => "background:#d9d9d9"
                                    ]),
                                \Filament\Forms\Components\TextInput::make('saldo.saldo_wajib')
                                    ->label('Saldo Tabungan Wajib')
                                    ->prefix('Rp .')
                                    ->default(0)
                                    ->readOnly()
                                    ->columnSpan(['lg' => 2, 'default' => 6])
                                    ->extraAttributes([
                                        'style' => "background:#d9d9d9"
                                    ]),
                                \Filament\Forms\Components\TextInput::make('saldo.saldo_sukarela')
                                    ->label('Saldo Tabungan Sukarela')
                                    ->prefix('Rp .')
                                    ->default(0)
                                    ->readOnly()
                                    ->columnSpan(['lg' => 2, 'default' => 6])
                                    ->extraAttributes([
                                        'style' => "background:#d9d9d9"
                                    ]),
                                \Filament\Forms\Components\TextInput::make('saldo.saldo_total')
                                    ->label('Saldo Total')
                                    ->prefix('Rp .')
                                    ->default(0)
                                    ->readOnly()
                                    ->columnSpan(['lg' => 6, 'default' => 6])
                                    ->extraAttributes([
                                        'style' => "background:#d9d9d9"
                                    ]),
                                \Filament\Forms\Components\TextInput::make('saldo.saldo_bonus')
                                    ->label('Saldo Bonus')
                                    ->prefix('Rp .')
                                    ->default(0)
                                    ->readOnly()
                                    ->columnSpan(['lg' => 6, 'default' => 6])
                                    ->extraAttributes([
                                        'style' => "background:#d9d9d9"
                                    ]),
                            ])
                            ->columns(6)
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto')
                    ->label('Foto')
                    ->circular()
                    ->openUrlInNewTab()
                    ->defaultImageUrl(url('/storage/avatars/default.png')),
                Tables\Columns\TextColumn::make('kode')
                    ->label('Kode Anggota')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nik')
                    ->label('Nomor Identitas')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('npwp')
                    ->label('Nomor Pajak')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('nama')
                    ->label('Nama Lengkap')
                    ->searchable(),
                Tables\Columns\TextColumn::make('saldsaldooAnggota.saldo_pokok')
                    ->label('Tabungan Pokok')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('saldo.saldo_wajib')
                    ->label('Tabungan Wajib')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('saldo.saldo_sukarela')
                    ->label('Tabungan Sukarela')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('saldo.saldo_bonus')
                    ->label('Saldo Bonus')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('saldo.saldo_total')
                    ->label('Saldo Total')
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
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
                        ->slideOver()
                        ->beforeFormFilled(function ($record) {
                            $pekerjaans = $record->pekerjaan;
                            $record['pekerjaan'] = [
                                'deskripsi' => $pekerjaans->deskripsi ?? null,
                                'alamat' => $pekerjaans->alamat ?? null,
                                'penghasilan' => $pekerjaans->penghasilan ?? null,
                            ];
                            $saldoAnggota = $record->saldo;
                            $record['saldo'] = [
                                'saldo_pokok' => $saldoAnggota->saldo_pokok ?? null,
                                'saldo_wajib' => $saldoAnggota->saldo_wajib ?? null,
                                'saldo_sukarela' => $saldoAnggota->saldo_sukarela ?? null,
                                'saldo_bonus' => $saldoAnggota->saldo_bonus ?? null,
                                'saldo_total' => $saldoAnggota->saldo_total ?? null,
                            ];
                            return $record;
                        }),
                    Tables\Actions\EditAction::make()
                        ->label('Ubah')
                        ->modalWidth(MaxWidth::Full)
                        ->slideOver()
                        ->beforeFormFilled(function ($record) {
                            // Siapkan Data Pekerjaan Sebelum Dimuat
                            $pekerjaans = $record->pekerjaan;
                            $record['pekerjaan'] = [
                                'deskripsi' => $pekerjaans->deskripsi ?? null,
                                'alamat' => $pekerjaans->alamat ?? null,
                                'penghasilan' => $pekerjaans->penghasilan ?? null,
                            ];
                            // Siapkan Data Saldo Sebelum Dimuat
                            $saldoAnggota = $record->saldo;
                            $record['saldo'] = [
                                'saldo_pokok' => $saldoAnggota->saldo_pokok ?? null,
                                'saldo_wajib' => $saldoAnggota->saldo_wajib ?? null,
                                'saldo_sukarela' => $saldoAnggota->saldo_sukarela ?? null,
                                'saldo_bonus' => $saldoAnggota->saldo_bonus ?? null,
                                'saldo_total' => $saldoAnggota->saldo_total ?? null,
                            ];
                            // Kirimkan Hasil
                            return $record;
                        })
                        ->using(function (array $data, $record) {
                            $anggotaActivityOld = $record;
                            $anggotaActivityOld['pekerjaan'] = \Illuminate\Support\Arr::except($record->pekerjaan, ['id', 'anggota_id', 'created_at', 'updated_at']);
                            $anggotaActivityOld['saldo'] = \Illuminate\Support\Arr::except($record->saldo, ['id', 'anggota_id', 'created_at', 'updated_at']);
                            $anggotaActivityNew = $data;

                            // Hapus Data Saldo Dari Array
                            unset($data['saldo']);

                            // Tangkap Data Pekerjaan
                            $pekerjaanData = $data['pekerjaan'];
                            unset($data['pekerjaan']);

                            // Tangkap Data Akun
                            unset($data['user']['email']);
                            $data['user']['name'] = $data['nama'];
                            $userData = $data['user'];
                            unset($data['user']);

                            // Ubah Data Akun
                            $userModel = \App\Models\User::withTrashed()->find($record->user_id)->update($userData);

                            // Ubah Data Anggota
                            $anggotaModel = Anggota::find($record->id);
                            $anggotaModel->update($data);

                            // Ubah Data Pekerjaan
                            $anggotaModel->pekerjaan->update($pekerjaanData);

                            // AnggotaActivity::updateAnggota($anggotaActivityOld, $anggotaActivityNew);
                        })
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\DeleteAction::make()
                        ->label('Hapus')
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\RestoreAction::make()
                        ->label('Kembalikan')
                        ->successRedirectUrl(self::getUrl()),
                    Tables\Actions\ForceDeleteAction::make()
                        ->label('Hapus Selamanya')
                        ->before(function ($record) {
                            $anggotaActivityOld = $record;
                            $anggotaActivityOld['pekerjaan'] = \Illuminate\Support\Arr::except($record->pekerjaan, ['id', 'anggota_id', 'created_at', 'updated_at']);
                            $anggotaActivityOld['saldo'] = \Illuminate\Support\Arr::except($record->saldo, ['id', 'anggota_id', 'created_at', 'updated_at']);
                            // AnggotaActivity::deleteAnggota($record);
                        })
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
        $link = ['index' => Pages\ListAnggotas::route('/')];
        if (Schema::hasTable('settings')) {
            if (!boolval(Setting::getFormSlide())) {
                $link += [
                    'create' => Pages\CreateAnggota::route('/create'),
                    'view' => Pages\ViewAnggota::route('/{record}'),
                    'edit' => Pages\EditAnggota::route('/{record}/edit'),
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
