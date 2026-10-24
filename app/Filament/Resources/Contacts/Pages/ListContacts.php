<?php

namespace App\Filament\Resources\Contacts\Pages;

use App\Filament\Resources\Contacts\ContactResource;
use App\Models\Account;
use App\Models\Contact;
use App\Services\SalesforceService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('import')
                ->label("Import from Salesforce")
                ->requiresConfirmation()
                ->action(function(){
                    $sf = app(SalesforceService::class);
                    $response = $sf->getContacts();

                    $data = $response->json();

                    $count = 0;

                    foreach($data['records'] as $item)
                    {
                        $contact = Contact::where('sf_contact_id', $item['Id'])->first();

                        if(!$contact)
                        {
                            $contact = new Contact;
                            $contact->sf_contact_id = $item['Id'];
                            $contact->sf_account_id = $item['AccountId'];
                            $contact->name = $item['Name'] ?? null;
                            $contact->first_name = $item['FirstName'] ?? null;
                            $contact->middle_name = $item['MiddleName'] ?? null;
                            $contact->last_name = $item['LastName'] ?? null;
                            $contact->salutation = $item['Salutation'] ?? null;
                            $contact->phone = $item['Phone'] ?? null;
                            $contact->email = $item['Email'] ?? null;
                            $contact->save();

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
