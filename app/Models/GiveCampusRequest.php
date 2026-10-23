<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GiveCampusRequest extends Model
{
    protected $table = 'givecampus_requests';

    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'data' => 'array',
    ];
}
