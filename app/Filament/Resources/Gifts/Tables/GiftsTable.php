<?php

namespace App\Filament\Resources\Gifts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GiftsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sf_opportunity_id')
                    ->label("Salesforce ID")
                    ->placeholder('Not synced'),
                TextColumn::make('project_name')
                    ->label("Campaign")
                    ->searchable(),
                TextColumn::make('givecampus_id')
                    ->label("Donation ID"),
                TextColumn::make('state')
                    ->label("State")
                    ->badge(),
                TextColumn::make('payer_name')
                    ->searchable(),
                TextColumn::make('payer_email')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->searchable(),
                TextColumn::make('value_usd')
                    ->label("Amount (USD)")
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gift_created_at')
                    ->label("Created At")
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('synced_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
