<?php

namespace App\Filament\Resources\SeatsResource\Pages;

use App\Filament\Resources\SeatsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeats extends EditRecord
{
    protected static string $resource = SeatsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
