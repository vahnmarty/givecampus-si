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

                TextColumn::make('project_name')
                    ->label("Campaign")
                    ->searchable(),
                TextColumn::make('givecampus_id')
                    ->label("Donation ID"),
                TextColumn::make('payer_name')
                    ->searchable(),
                TextColumn::make('payer_email')
                    ->searchable(),
                TextColumn::make('phone_number')
                    ->searchable(),
                IconColumn::make('anonymous')
                    ->boolean(),
                TextColumn::make('value')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('currency')
                    ->searchable(),
                TextColumn::make('value_usd')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('gift_created_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('gift_updated_at')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('checkout_at')
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
