<?php

namespace App\Filament\Resources\Gifts\Tables;

use App\Models\Contact;
use App\Models\Gift;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class GiftsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sf_opportunity_id')
                    ->label("Salesforce ID")
                    ->placeholder('Not synced'),
                TextColumn::make('sf_contact_search_type')
                    ->label("Search State")
                    ->badge(),
                TextColumn::make('project_name')
                    ->label("Campaign")
                    ->limit(30)
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
                Action::make('match')
                    ->action(function(Gift $record, array $data){

                        $result = $record->localSearch();


                        $record->sf_contact_found = $result['success'];
                        $record->sf_contact_id = $result['record']['sf_contact_id'] ?? null;
                        $record->sf_contact_search_type = $result['method'];
                        $record->save();

                        Notification::make()
                            ->info()
                            ->title('Search Method: ' . $result['method'])
                            ->send();
                    })
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('match_selected')
                        ->label('Cross-check')
                        ->icon('heroicon-o-bolt')
                        ->modalDescription('This will cross check gifts record to in-app records.')
                        ->requiresConfirmation()
                        ->action(function (Collection $records){

                            $successCount = 0;
                            $failedCount = 0;

                            foreach($records as $gift){
                                $result = $gift->localSearch();

                                $gift->sf_contact_found = $result['success'];
                                $gift->sf_contact_id = $result['record']['sf_contact_id'] ?? null;
                                $gift->sf_contact_search_type = $result['method'];
                                $gift->save();

                                if($result['success']){
                                    $successCount++;
                                }else{
                                    $failedCount++;
                                }

                            }

                            Notification::make()
                                ->info()
                                ->title('Matching complete')
                                ->body('Success: ' . $successCount . '. Failed : ' . $failedCount)
                                ->send();
                        }),
                    //DeleteBulkAction::make(),
                ]),
            ]);
    }
}
