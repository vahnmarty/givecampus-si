<?php
namespace App\Filament\Resources\Gifts\Pages;

use App\Filament\Resources\Gifts\GiftResource;
use App\Models\Gift;
use App\Services\GiveCampusService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Throwable;

class ListGifts extends ListRecords
{
    protected static string $resource = GiftResource::class;

    protected string $view = 'filament.resources.gifts.list-gifts';

    public ?string $status_gift = 'Pending';
    public ?string $status_request = 'Pending';
    public ?string $status_download = 'Pending';

    public ?int $start_date = null;
    public ?int $end_date = null;

    public ?string $request_id = null;
    public ?string $download_url = null;

    public ?string $sync_error = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('generate_request')
                ->label('Generate Gifts Request')
                ->modalSubmitActionLabel('Generate')
                ->schema([
                    DateTimePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->default(now()->subDays(30)),

                    DateTimePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->default(now()),
                ])
                ->action(function (array $data): void {
                    $this->start_date = Carbon::parse(
                        $data['start_date']
                    )->unix();

                    $this->end_date = Carbon::parse(
                        $data['end_date']
                    )->unix();

                }),
            Action::make('generate_result')
                ->label('Generate Result')
                ->modalSubmitActionLabel('Generate')
                ->schema([
                    TextInput::make('request_id')
                        ->placeholder('0000-0000-0000-0000')
                        ->label('Request ID')
                        ->required()
                ])
                ->action(function (array $data): void {

                }),
            Action::make('import')
                ->label('Import from GiveCampus')
                ->modalSubmitActionLabel('Generate')
                ->schema([
                    DateTimePicker::make('start_date')
                        ->label('Start Date')
                        ->required()
                        ->default(now()->subDays(30)),

                    DateTimePicker::make('end_date')
                        ->label('End Date')
                        ->required()
                        ->default(now()),
                ])
                ->action(function (array $data): void {
                    $this->start_date = Carbon::parse(
                        $data['start_date']
                    )->unix();

                    $this->end_date = Carbon::parse(
                        $data['end_date']
                    )->unix();

                    $this->status_gift = 'Pending';
                    $this->status_request = 'Pending';
                    $this->status_download = 'Pending';

                    $this->request_id = null;
                    $this->download_url = null;
                    $this->sync_error = null;

                    $this->dispatch(
                        'open-modal',
                        id: 'givecampus'
                    );
                })
                ->color('primary'),
        ];
    }

    public function getGifts()
    {
        $this->validate([
            'start_date' => ['required', 'integer'],
            'end_date' => ['required', 'integer'],
        ]);

        try {
            $gc = new GiveCampusService();

            $gc->setStartDate($this->start_date);
            $gc->setEndDate($this->end_date);

            $response = $gc->getGifts();

            $this->status_gift = $response['status'] ?? 'Submitted';
            $this->request_id = $response['request_id'] ?? null;

            if (! $this->request_id) {
                throw new \RuntimeException(
                    'GiveCampus did not return a request ID.'
                );
            }

            return [
                'success' => true,
                'status' => $this->status_gift,
            ];
        } catch (Throwable $e) {
            report($e);

            $this->status_gift = 'Failed';
            $this->sync_error = $e->getMessage();

            return ['success' => false];
        }
    }

    public function getRequest(): array
    {
        if (! $this->request_id) {
            return ['success' => false];
        }

        try {
            $gc = new GiveCampusService();

            $response = $gc->getRequest($this->request_id);

            $this->status_request = $response['status'] ?? 'Unknown';

            if ($this->status_request === 'completed') {
                $this->download_url = $response['download_url'] ?? null;
            }

            if (in_array(
                $this->status_request,
                ['failed', 'error'],
                true
            )) {
                $this->sync_error = 'GiveCampus request failed.';
            }

            return [
                'success' => ! in_array(
                    $this->status_request,
                    ['failed', 'error'],
                    true
                ),
                'status' => $this->status_request,
            ];
        } catch (Throwable $e) {
            report($e);

            $this->status_request = 'Failed';
            $this->sync_error = $e->getMessage();

            return ['success' => false, 'status' => 'failed'];
        }
    }

    public function getDownload(): array
    {
        if (! $this->download_url) {
            return ['success' => false];
        }

        try {
            $gc = new GiveCampusService();

            $gifts = $gc->getDownloadUrl($this->download_url);

            $this->status_download = 'Downloaded';

            return [
                'success' => true,
            ];
        } catch (Throwable $e) {
            report($e);

            $this->status_download = 'Failed';
            $this->sync_error = $e->getMessage();

            return ['success' => false];
        }
    }

    public function syncData(): void
    {
        $gc = new GiveCampusService();

        $gifts = $gc->getDownloadUrl($this->download_url)->json();


        foreach ($gifts as $gift_api) {
            $gift = Gift::where('givecampus_id', $gift_api['id'])->first();


            if(!$gift){

                try {
                    $gift = new Gift;
                    $gift->givecampus_id = $gift_api['id'];
                    $gift->request_id = $this->request_id;
                    $gift->payer_name = $gift_api['payer_name'] ?? null;
                    $gift->payer_email = $gift_api['payer_email'] ?? null;
                    $gift->paid_name = $gift_api['paid_name'] ?? null;
                    $gift->paid_email = $gift_api['paid_email'] ?? null;
                    $gift->phone_number = $gift_api['phone_number'] ?? null;
                    $gift->anonymous = $gift_api['anonymous'] ?? false;
                    $gift->value = $gift_api['value'] ?? null;
                    $gift->currency = $gift_api['currency'] ?? null;
                    $gift->value_usd = $gift_api['value_usd'] ?? null;
                    $gift->donation_type = $gift_api['donation_type'] ?? null;
                    $gift->state = $gift_api['state'] ?? null;
                    $gift->payment_method = $gift_api['payment_method'] ?? null;
                    $gift->card_type = $gift_api['card_type'] ?? null;
                    $gift->checkout_id = $gift_api['checkout_id'] ?? null;
                    $gift->refund_status = $gift_api['refund_status'] ?? null;
                    $gift->project_id = $gift_api['project']['id'] ?? null;
                    $gift->project_type = $gift_api['project']['type'] ?? null;
                    $gift->project_name = $gift_api['project']['name'] ?? null;
                    $gift->gift_created_at = isset($gift_api['created_at']) ? Carbon::parse($gift_api['created_at']) : null;
                    $gift->gift_updated_at = isset($gift_api['updated_at']) ? Carbon::parse($gift_api['updated_at']) : null;
                    $gift->payload = $gift_api;
                    $gift->synced_at = now();
                    $gift->save();
                } catch (\Throwable $th) {
                    throw $th;
                }

            }
        }


        Notification::make()
            ->title('Gifts synchronized successfully.')
            ->success()
            ->send();

        $this->dispatch('close-modal', id: 'givecampus');

    }
}
