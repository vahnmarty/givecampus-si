<?php

namespace App\Filament\Resources\GivecampusRequests\Pages;

use App\Filament\Resources\GivecampusRequests\GivecampusRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditGivecampusRequest extends EditRecord
{
    protected static string $resource = GivecampusRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
