<?php

namespace App\Filament\Resources\GiftRequests\Tables;

use App\Models\GiftRequest;
use App\Services\GiveCampusService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class GiftRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('payload')
                    ->label('Date Range')
                    ->state(fn(GiftRequest $record) => $record->getDateRange()),

                TextColumn::make('status')
                    ->searchable(),
                TextColumn::make('request_id')
                    ->searchable(),
                TextColumn::make('download_url')
                    ->limit(50)
                    ->placeholder('Incomplete'),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('generate_request')
                    ->label('Get Request')
                    ->hidden(fn(GiftRequest $record) => !empty($record->download_url))
                    ->icon('heroicon-o-bolt')
                    ->requiresConfirmation()
                    ->action(function (GiftRequest  $record): void {
                        $gc = $gc = new GiveCampusService;
                        $response = $gc->getRequest($record->request_id)->json();

                        $status = $response['status'] ?? null;

                        do {
                            if ($status === 'completed') {
                                break;
                            }

                            $response = $gc->getRequest($record->request_id);

                            $status = $response['status'] ?? null;

                        } while ($status === 'in_progress');


                        $download_url = $response['download_url'] ?? null;

                        $record->download_url = $download_url;
                        $record->save();

                        Notification::make()
                            ->success()
                            ->title('Download URL has been created!')
                            ->send();

                    }),
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
