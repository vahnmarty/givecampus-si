<?php

namespace App\Filament\Resources\GiftRequests\Pages;

use App\Filament\Resources\GiftRequests\GiftRequestResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditGiftRequest extends EditRecord
{
    protected static string $resource = GiftRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
