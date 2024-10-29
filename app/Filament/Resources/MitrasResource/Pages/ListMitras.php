<?php

namespace App\Filament\Resources\MitrasResource\Pages;

use App\Filament\Resources\MitrasResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMitras extends ListRecords
{
    protected static string $resource = MitrasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
