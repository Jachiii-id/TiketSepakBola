<?php

namespace App\Filament\Resources\MitrasResource\Pages;

use App\Filament\Resources\MitrasResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMitras extends EditRecord
{
    protected static string $resource = MitrasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
