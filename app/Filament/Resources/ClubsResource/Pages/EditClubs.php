<?php

namespace App\Filament\Resources\ClubsResource\Pages;

use App\Filament\Resources\ClubsResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditClubs extends EditRecord
{
    protected static string $resource = ClubsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
