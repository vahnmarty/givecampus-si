<?php

namespace App\Filament\Resources\Accounts\Pages;

use App\Filament\Resources\Accounts\AccountResource;
use App\Models\Account;
use App\Services\SalesforceService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAccounts extends ListRecords
{
    protected static string $resource = AccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('import')
                ->label("Import from Salesforce")
                ->requiresConfirmation()
                ->action(function(){
                    $sf = app(SalesforceService::class);
                    $response = $sf->getAccounts();

                    $data = $response->json();

                    $count = 0;

                    foreach($data['records'] as $item)
                    {
                        $account = Account::where('salesforce_id', $item['Id'])->first();

                        if(!$account)
                        {
                            $account = new Account;
                            $account->salesforce_id = $item['Id'];
                            $account->name = $item['Name'];
                            $account->save();

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
