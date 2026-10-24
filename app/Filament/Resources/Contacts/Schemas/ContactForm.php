<?php

namespace App\Filament\Resources\Contacts\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sf_contact_id')
                    ->required(),
                TextInput::make('sf_account_id'),
                TextInput::make('name'),
                TextInput::make('salutation'),
                TextInput::make('first_name'),
                TextInput::make('middle_name'),
                TextInput::make('last_name'),
                TextInput::make('phone')
                    ->tel(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email(),
            ]);
    }
}
