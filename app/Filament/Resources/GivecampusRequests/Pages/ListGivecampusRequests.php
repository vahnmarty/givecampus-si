<?php

namespace App\Filament\Resources\GivecampusRequests\Pages;

use App\Filament\Resources\GivecampusRequests\GivecampusRequestResource;
use App\Services\GiveCampusService;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ListGivecampusRequests extends ListRecords
{
    protected static string $resource = GivecampusRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false)
                ->modalSubmitActionLabel('Send')
                ->using(function (array $data, string $model): Model {

                    $array = [];
                    $start_date = Carbon::parse($data['start_date'])->unix();
                    $end_date = Carbon::parse($data['end_date'])->unix();

                    $gc = new GiveCampusService;
                    $gc->setStartDate($start_date);
                    $gc->setEndDate($end_date);

                    $response = $gc->getPaidGifts()->json();

                    $array['payload'] = $gc->getParams();
                    $array['status'] = $response['status'];
                    $array['request_id'] = $response['request_id'];

                    return $model::create($array);
                }),
        ];
    }
}
