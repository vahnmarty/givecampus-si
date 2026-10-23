<?php

namespace App\Filament\Resources\GivecampusRequests;

use App\Filament\Resources\GivecampusRequests\Pages\CreateGivecampusRequest;
use App\Filament\Resources\GivecampusRequests\Pages\EditGivecampusRequest;
use App\Filament\Resources\GivecampusRequests\Pages\ListGivecampusRequests;
use App\Filament\Resources\GivecampusRequests\Pages\ViewGivecampusRequest;
use App\Filament\Resources\GivecampusRequests\Schemas\GivecampusRequestForm;
use App\Filament\Resources\GivecampusRequests\Tables\GivecampusRequestsTable;
use App\Models\GivecampusRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class GivecampusRequestResource extends Resource
{
    protected static ?string $model = GivecampusRequest::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::Bolt;

    protected static ?string $navigationLabel = 'GiveCampus API';


    protected static ?string $recordTitleAttribute = 'request_id';

    public static function form(Schema $schema): Schema
    {
        return GivecampusRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return GivecampusRequestsTable::configure($table);
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
            'index' => ListGivecampusRequests::route('/'),
            'view' => ViewGivecampusRequest::route('/{record}'),
            'edit' => EditGivecampusRequest::route('/{record}/edit'),
            //'create' => CreateGivecampusRequest::route('/create'),
        ];
    }
}
