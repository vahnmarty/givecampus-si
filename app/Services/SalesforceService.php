<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SalesforceService
{
    private string $apiUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->apiUrl = config('services.salesforce.url');
        $this->apiKey = config('services.salesforce.token');
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getOpportunities(): Response
    {
        $url = $this->apiUrl . '/services/data/v68.0/queryAll/?q=SELECT+Id,Name,Amount,StageName,CloseDate+FROM+Opportunity';

        $api = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->get($url);

        return $api;
    }

}
