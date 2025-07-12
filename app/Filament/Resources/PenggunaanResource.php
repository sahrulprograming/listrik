<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Pelanggan;
use App\Models\Penggunaan;
use Filament\Tables\Table;
use Illuminate\Support\Carbon;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PenggunaanResource\Pages;
use App\Filament\Resources\PenggunaanResource\RelationManagers;

class PenggunaanResource extends Resource
{
    protected static ?string $model = Penggunaan::class;

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $label = 'Pengunaan';

    protected static ?string $navigationLabel = 'Penggunaan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('id_pelanggans')
                    ->required()
                    ->label('Nama Pelanggan')
                    ->options(Pelanggan::all()->pluck('nama_pelanggan', 'id'))
                    ->columnSpanFull(),
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
                    ->label('Bulan Bayar'),
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
                Forms\Components\TextInput::make('meter_awal')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('meter_akhir')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('active')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('pelanggan.nama_pelanggan')
                    ->sortable(),
                TextColumn::make('bulan')
                    ->label('Bulan'),
                Tables\Columns\TextColumn::make('tahun'),
                Tables\Columns\TextColumn::make('meter_awal')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('meter_akhir')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListPenggunaans::route('/'),
            'create' => Pages\CreatePenggunaan::route('/create'),
            'edit' => Pages\EditPenggunaan::route('/{record}/edit'),
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
