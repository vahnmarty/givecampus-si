<?php

namespace App\Filament\Resources\GiftRequests;

use App\Filament\Resources\GiftRequests\Pages\CreateGiftRequest;
use App\Filament\Resources\GiftRequests\Pages\EditGiftRequest;
use App\Filament\Resources\GiftRequests\Pages\ListGiftRequests;
use App\Filament\Resources\GiftRequests\Pages\ViewGiftRequest;
use App\Filament\Resources\GiftRequests\Schemas\GiftRequestForm;
use App\Filament\Resources\GiftRequests\Schemas\GiftRequestInfolist;
use App\Filament\Resources\GiftRequests\Tables\GiftRequestsTable;
use App\Models\GiftRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GiftRequestResource extends Resource
{
    protected static ?string $model = GiftRequest::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $recordTitleAttribute = 'request_id';

    public static function form(Schema $schema): Schema
    {
        return GiftRequestForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return GiftRequestInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GiftRequestsTable::configure($table);
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
            'index' => ListGiftRequests::route('/'),
            //'create' => CreateGiftRequest::route('/create'),
            'view' => ViewGiftRequest::route('/{record}'),
            'edit' => EditGiftRequest::route('/{record}/edit'),
        ];
    }
}
