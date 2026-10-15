<?php

namespace App\Models;

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
}
