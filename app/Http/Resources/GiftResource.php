<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GiftResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'givecampus_id' => $this->givecampus_id,
            'request_id' => $this->request_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'payer_name' => $this->payer_name,
            'payer_email' => $this->payer_email,
            'paid_name' => $this->paid_name,
            'value_usd' => $this->value_usd,
            'state' => $this->state,
            'checkout_id' => $this->checkout_id,
            'project_id' => $this->project_id,
            'project_name' => $this->project_name,
            'gift_created_at' => $this->gift_created_at,
            'gift_updated_at' => $this->gift_updated_at,
            'sf_contact_found' => $this->sf_contact_found,
            'sf_contact_id' => $this->sf_contact_id,
            'sf_contact_search_type' => $this->sf_contact_search_type,
            'sf_opportunity_found' => $this->sf_opportunity_found,
        ];
    }
}
