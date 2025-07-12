<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Tagihan;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Pelanggan;
use App\Models\Penggunaan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TagihanResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\TagihanResource\RelationManagers;

class TagihanResource extends Resource
{
    protected static ?string $model = Tagihan::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $label = 'Tagihan';

    protected static ?string $navigationLabel = 'Tagihan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_penggunaans')
                    ->label('Id Penggunaan')
                    ->required()
                    ->columnSpanFull()
                    ->searchable()
                    ->options(Penggunaan::all()->pluck('id', 'id'))
                    ->dehydrated(fn ($state) => filled($state))
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        // Log untuk debugging
                        Log::info("Penggunaan ID entered: " . $state);

                        // Temukan data penggunaan berdasarkan ID
                        $penggunaan = \App\Models\Penggunaan::find($state);
                        if ($penggunaan) {
                            Log::info("Penggunaan found: ", $penggunaan->toArray());
                            // Menghitung jumlah meter (meter_akhir - meter_awal)
                            $jumlahMeter = $penggunaan->meter_akhir - $penggunaan->meter_awal;

                            $set('bulan', $penggunaan->bulan);
                            $set('tahun', $penggunaan->tahun);
                            $set('meter_awal', $penggunaan->meter_awal);
                            $set('meter_akhir', $penggunaan->meter_akhir);
                            $set('id_pelanggans', $penggunaan->id_pelanggans);
                            $set('jumlah_meter', $jumlahMeter);

                        } else {
                            Log::info("Penggunaan not found");
                        }
                    }),
                Select::make('id_pelanggans')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->columnSpanFull()
                    ->searchable()
                    ->options(Penggunaan::with('pelanggan')->get()->pluck('pelanggan.nama_pelanggan', 'id_pelanggans')),
                Select::make('bulan')
                    ->options([
                        'Januari' => 'Januari',
                        'Februari' => 'Februari',
                        'Maret' => 'Maret',
                        'April' => 'April',
                        'Mei' => 'Mei',
                        'Juni' => 'Juni',
                        'Juli' => 'Juli',
                        'Agustus' => 'Agustus',
                        'September' => 'September',
                        'Oktober' => 'Oktober',
                        'November' => 'November',
                        'Desember' => 'Desember',
                    ])
                    ->required()
                    ->label('Bulan'),
                Select::make('tahun')
                    ->required()
                    ->options(function () {
                        $currentYear = now()->year;
                        // 12 tahun kebelakang
                        return collect(range($currentYear - 12, $currentYear))
                            ->mapWithKeys(fn($year) => [$year => $year])
                            ->reverse();
                    })
                    ->default(now()->year)
                    ->searchable()
                    ->preload(),
                Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('jumlah_meter')
                            ->required()
                            ->numeric(),
                        Forms\Components\TextInput::make('biaya_tagihan')
                            ->required()
                            ->numeric(),
                        Select::make('status')
                            ->label('Status Tagihan')
                            ->required()
                            ->options(Tagihan::STATUS_OPTIONS)
                            ->default('belum_bayar')
                            ->placeholder('Pilih Status'),
                ])->columns('3'),
                Forms\Components\Toggle::make('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_penggunaans')
                    ->label('No Penggunaan')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('pelanggan.nama_pelanggan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('bulan')
                ->label('Bulan'),
                Tables\Columns\TextColumn::make('tahun')
                    ->sortable(),
                Tables\Columns\TextColumn::make('jumlah_meter')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_tagihan')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('creator_name')
                    ->label('Created By')
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_name')
                    ->label("Updated by")
                    ->sortable(),
                Tables\Columns\TextColumn::make('deleted_name')
                    ->label("Deleted by")
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
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
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
            'index' => Pages\ListTagihans::route('/'),
            'create' => Pages\CreateTagihan::route('/create'),
            'edit' => Pages\EditTagihan::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class]);

        if (Auth::check() && Auth::user()->level->id === 2) {
            $userId = Auth::id();
            $customerIds = Pelanggan::where('id_users', $userId)->pluck('id')->toArray();
            $query->whereIn('id_pelanggans', $customerIds);
        }

        return $query;
    }
}
