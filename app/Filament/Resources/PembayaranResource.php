<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Tagihan;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Penggunaan;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PembayaranResource\Pages;
use App\Filament\Resources\PembayaranResource\RelationManagers;

class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $label = 'Pembayaran';

    protected static ?string $navigationLabel = 'Pembayaran';

    public static function form(Form $form): Form
    {
        $User = \App\Models\User::find(request('id_users'));

        return $form
            ->schema([
                Select::make('id_tagihans')
                    ->required()
                    ->searchable()
                    ->label('Nama Pelanggan')
                    ->options(Tagihan::with('pelanggan')->get()->pluck('pelanggan.nama_pelanggan', 'id'))
                    ->columnSpanFull()
                    ->dehydrated(fn ($state) => filled($state))
                    ->reactive()
                    ->afterStateUpdated(function (Set $set, Get $get, $state) {
                        // Log untuk debugging
                        Log::info("Penggunaan ID entered: " . $state);

                        // Temukan data penggunaan berdasarkan ID
                        $tagihan= \App\Models\Tagihan::find($state);
                        if ($tagihan) {
                            Log::info("Penggunaan found: ", $tagihan->toArray());
                            // Menghitung jumlah meter (meter_akhir - meter_awal)
                            $biayaAdmin = \App\Models\Pembayaran::where('id_tagihans', $state)->value('biaya_admin');
                            $totalBayar = $tagihan->biaya_tagihan + ($biayaAdmin ?? 0);

                            $set('biaya_tagihan', $tagihan->biaya_tagihan);
                            $set('id_pelanggans', $tagihan->id_pelanggans);
                            $set('bulan', $tagihan->bulan);
                            $set('total_bayar', $totalBayar);

                        } else {
                            Log::info("Penggunaan not found");
                        }
                    }),
                Select::make('id_pelanggans')
                    ->label('Nama Pelanggan')
                    ->required()
                    ->options(Penggunaan::with('pelanggan')->get()->pluck('pelanggan.nama_pelanggan', 'id_pelanggans')),
                Forms\Components\TextInput::make('id_users')
                    ->required()
                    ->label('Nama Admin')
                    ->default(function () {
                        $user = Auth::user();
                        return $user ? $user->name : 'Default Name'; // Mengambil nama dari pengguna yang sedang login atau menetapkan nilai default
                    })
                    ->disabled(),
                Forms\Components\DatePicker::make('tanggal_pembayaran')
                    ->required(),
                Select::make('bulan_bayar')
                    ->required()
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
                    ->label('Bulan Bayar'),
                Grid::make()
                    ->schema([
                        Forms\Components\TextInput::make('biaya_tagihan')
                            ->required(),
                        Forms\Components\TextInput::make('biaya_admin')
                            ->required()
                            ->default('5000'),
                        Forms\Components\TextInput::make('total_bayar')
                            ->required()
                            ->numeric(),
                    ])->columns('3'),
                Forms\Components\Toggle::make('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pelanggan.nama_pelanggan')
                    ->label('Nama Pelanggan')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Nama Admin')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tanggal_pembayaran')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bulan_bayar')
                    ->sortable(),
                Tables\Columns\TextColumn::make('biaya_tagihan')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('biaya_admin')
                    ->numeric()
                    ->default('5000')
                    ->sortable()
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('total_bayar')
                    ->numeric()
                    ->sortable()
                    ->money('IDR'),
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
            'index' => Pages\ListPembayarans::route('/'),
            'create' => Pages\CreatePembayaran::route('/create'),
            'edit' => Pages\EditPembayaran::route('/{record}/edit'),
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
