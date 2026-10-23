<?php

namespace App\Filament\Resources\GiftRequests\Pages;

use App\Filament\Resources\GiftRequests\GiftRequestResource;
use App\Models\Gift;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use App\Models\GiftRequest;
use App\Services\GiveCampusService;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class ViewGiftRequest extends ViewRecord
{
    protected static string $resource = GiftRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('fetch')
                ->label('Fetch Data')
                ->action(function(GiftRequest $record){
                    $gc = new GiveCampusService;
                    $response = $gc->getDownloadUrl($record->download_url);

                    $record->data =  $response->json();
                    $record->completed_at = now();
                    $record->status = 'completed';
                    $record->save();

                    $directory = database_path('json');

                    $filename = "{$record->request_id}.json";
                    $path = "{$directory}/{$filename}";

                    File::put(
                        $path,
                        json_encode($response->json(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                    );
                })
                ->hidden(fn(GiftRequest $record) => !empty($record->completed_at)),

            Action::make('download_json')
                ->label('Download JSON')
                ->icon('heroicon-o-arrow-down-tray')
                ->visible(fn(GiftRequest $record) => !empty($record->completed_at))
                ->action(function (GiftRequest $record) {
                    $filename = $record->request_id . '.json';

                    $path = database_path("json/{$filename}");

                    abort_unless(File::exists($path), 404, 'File not found.');

                    return response()->download($path);
                }),

            Action::make('sync')
                ->label('Sync to System')
                ->icon('heroicon-o-arrow-down-on-square')
                ->requiresConfirmation()
                ->visible(fn(GiftRequest $record) => !empty($record->completed_at))
                ->action(function (GiftRequest $record) {
                    $array = $record->data;

                    $count = 0;

                    foreach ($array as $item) {
                        $gift = Gift::where('givecampus_id', $item['id'])->first();


                        if(!$gift){

                            try {
                                $gift = new Gift;
                                $gift->givecampus_id = $item['id'];
                                $gift->request_id = $record->request_id;
                                $gift->payer_name = $item['payer_name'] ?? null;
                                $gift->payer_email = $item['payer_email'] ?? null;
                                $gift->paid_name = $item['paid_name'] ?? null;
                                $gift->paid_email = $item['paid_email'] ?? null;
                                $gift->phone_number = $item['phone_number'] ?? null;
                                $gift->anonymous = $item['anonymous'] ?? false;
                                $gift->value = $item['value'] ?? null;
                                $gift->currency = $item['currency'] ?? null;
                                $gift->value_usd = $item['value_usd'] ?? null;
                                $gift->donation_type = $item['donation_type'] ?? null;
                                $gift->state = $item['state'] ?? null;
                                $gift->payment_method = $item['payment_method'] ?? null;
                                $gift->card_type = $item['card_type'] ?? null;
                                $gift->checkout_id = $item['checkout_id'] ?? null;
                                $gift->refund_status = $item['refund_status'] ?? null;
                                $gift->project_id = $item['project']['id'] ?? null;
                                $gift->project_type = $item['project']['type'] ?? null;
                                $gift->project_name = $item['project']['name'] ?? null;
                                $gift->gift_created_at = isset($item['timestamps']['created_at']) ? Carbon::parse($item['timestamps']['created_at']) : null;
                                $gift->gift_updated_at = isset($item['timestamps']['updated_at']) ? Carbon::parse($item['timestamps']['updated_at']) : null;
                                $gift->payload = $item;
                                $gift->imported_at = now();
                                $gift->save();

                                $count++;
                            } catch (\Throwable $th) {
                                throw $th;
                            }

                        }
                    }


                    Notification::make()
                        ->success()
                        ->title($count . ' records has been added to the system.')
                        ->send();
                })
        ];
    }
}
