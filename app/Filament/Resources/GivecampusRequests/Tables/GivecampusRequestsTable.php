<?php

namespace App\Filament\Resources\GivecampusRequests\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Models\GiveCampusRequest;
use App\Services\GiveCampusService;
use Filament\Notifications\Notification;

class GivecampusRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime()
                    ->since(),
                TextColumn::make('request_id')
                    ->label('Request ID')
                    ->copyable()
                    ->color('primary')
                    ->extraAttributes(['class' => 'hover:underline']),
                TextColumn::make('status'),
                TextColumn::make('download_url')
                    ->wrap()
                    ->hidden()
                    ->copyable()
                    ->placeholder('Generate')
                    ->action(
                        Action::make('generate')
                            ->requiresConfirmation()
                            ->action(function (GiveCampusRequest  $record): void {
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
                    ),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
