<?php

namespace App\Filament\Resources\GivecampusRequests\Pages;

use App\Filament\Resources\GivecampusRequests\GivecampusRequestResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewGivecampusRequest extends ViewRecord
{
    protected static string $resource = GivecampusRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
