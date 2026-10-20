<?php

namespace App\Filament\Resources\Gifts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class GiftForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('givecampus_id')
                    ->required()
                    ->numeric(),
                TextInput::make('request_id'),
                TextInput::make('payer_name'),
                TextInput::make('payer_email')
                    ->email(),
                TextInput::make('paid_name'),
                TextInput::make('paid_email')
                    ->email(),
                TextInput::make('phone_number')
                    ->tel(),
                Toggle::make('anonymous')
                    ->required(),
                TextInput::make('value')
                    ->numeric(),
                TextInput::make('currency'),
                TextInput::make('value_usd')
                    ->numeric(),
                TextInput::make('donation_type'),
                TextInput::make('state'),
                TextInput::make('payment_method'),
                TextInput::make('card_type'),
                TextInput::make('checkout_id'),
                TextInput::make('refund_status'),
                TextInput::make('project_id')
                    ->numeric(),
                TextInput::make('project_type'),
                TextInput::make('project_name'),
                DateTimePicker::make('gift_created_at'),
                DateTimePicker::make('gift_updated_at'),
                DateTimePicker::make('checkout_at'),
                TextInput::make('payload'),
                DateTimePicker::make('synced_at'),
            ]);
    }
}
