<?php

namespace App\Filament\Resources\GiftRequests\Schemas;

use Filament\Infolists\Components\CodeEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Phiki\Grammar\Grammar;
use App\Models\GiftRequest;

class GiftRequestInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextEntry::make('status')
                    ->placeholder('-'),
                CodeEntry::make('payload')
                    ->grammar(Grammar::Json),
                TextEntry::make('request_id')
                    ->placeholder('-')
                    ->copyable()
                    ->color('primary'),
                TextEntry::make('download_url')
                    ->placeholder('-')
                    ->copyable()
                    ->color('primary'),
                TextEntry::make('request_id')
                    ->placeholder('-')
                    ->formatStateUsing(fn(GiftRequest $record) => $record->request_id . '.json'),
                TextEntry::make('completed_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->since()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
