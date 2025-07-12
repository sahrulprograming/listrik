<?php

namespace App\Filament\Resources\TarifResource\Pages;

use App\Filament\Resources\TarifResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTarifs extends ListRecords
{
    protected static string $resource = TarifResource::class;

    protected static ?string $title = 'Tarif';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
