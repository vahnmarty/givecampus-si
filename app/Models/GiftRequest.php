<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class GiftRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'payload' => 'array',
        'data' => 'array',
    ];

    public function getDateRange()
    {
        $payload = $this->payload;

        return Carbon::parse($payload['start'])->format('m/d/Y') . ' - ' . Carbon::parse($payload['end'])->format('m/d/Y');
    }
}
