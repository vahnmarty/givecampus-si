<?php

namespace App\Filament\Resources\Opportunities\Pages;

use App\Filament\Resources\Opportunities\OpportunityResource;
use App\Models\Contact;
use App\Models\Opportunity;
use App\Services\SalesforceService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListOpportunities extends ListRecords
{
    protected static string $resource = OpportunityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label("Import from Salesforce")
                ->requiresConfirmation()
                ->action(function(){
                    $sf = app(SalesforceService::class);
                    $response = $sf->getOpportunities();

                    $data = $response->json();

                    $count = 0;

                    foreach($data['records'] as $item)
                    {
                        $opModel = Opportunity::where('sf_opportunity_id', $item['Id'])->first();

                        if(!$opModel)
                        {
                            $opModel = new Opportunity;
                            $opModel->sf_opportunity_id = $item['Id'];
                            $opModel->name = $item['Name'];
                            $opModel->amount = $item['Amount'];
                            $opModel->stage_name = $item['StageName'] ?? null;
                            $opModel->close_date = $item['CloseDate'] ?? null;
                            $opModel->save();

                            $count++;
                        }
                    }

                    Notification::make()
                        ->success()
                        ->title($count . ' records has been synced.')
                        ->send();
                })
        ];
    }
}
