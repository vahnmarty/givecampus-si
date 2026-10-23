<?php

namespace App\Filament\Resources\GivecampusRequests\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;

class GivecampusRequestForm
{
    public static function configure(Schema $schema): Schema
    {
        $bools = ['true' => 'True', 'false' => 'False', null => 'null'];
        return $schema
            ->columns(1)
            ->components([
                DateTimePicker::make('start_date')
                    ->required()
                    ->default(now()->subMonth()),
                DateTimePicker::make('end_date')
                    ->required()
                    ->default(now()),
                Select::make('paid')
                    ->options($bools)
                    ->default('true')
            ]);
    }
}
