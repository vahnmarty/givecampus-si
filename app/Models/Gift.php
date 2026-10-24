<?php

namespace App\Models;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'anonymous' => 'boolean',
        'gift_created_at' => 'datetime',
        'gift_updated_at' => 'datetime',
        'checkout_at' => 'datetime',
        'synced_at' => 'datetime',
    ];


    public function localSearch()
    {
        $record = $this;

        $sfContact = Contact::where('first_name', $record->first_name)
            ->where('last_name', $record->last_name)
            ->first();

        $searchMethod = 'BASIC_SEARCH';

        if(!$sfContact){
            $sfContact = Contact::where('first_name', 'LIKE', '%' . $record->first_name . '%')
            ->where('last_name',  'LIKE', '%' . $record->last_name . '%')
            ->first();

            $searchMethod = 'FUZZY_SEARCH_FULL_NAME';
        }

        if(!$sfContact){
            $sfContact = Contact::where('name', 'LIKE', '%' . $record->payer_name . '%')->first();

            $searchMethod = 'FUZZY_SEARCH_NAME';
        }

        if(!$sfContact){
            $searchMethod = 'NOT_FOUND';
        }

        return [
            'success' => !empty($sfContact) ? true : false,
            'method' => $searchMethod,
            'record' => $sfContact?->toArray() ?? [],
        ];
    }
}
