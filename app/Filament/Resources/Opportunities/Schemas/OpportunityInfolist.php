<?php

namespace App\Filament\Resources\Opportunities\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class OpportunityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('sf_opportunity_id'),
                TextEntry::make('name'),
                TextEntry::make('amount')
                    ->numeric(),
                TextEntry::make('stage_name')
                    ->placeholder('-'),
                TextEntry::make('close_date')
                    ->date()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
