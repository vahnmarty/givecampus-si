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

    public function createToken()
    {
        $url = $this->apiUrl . '/services/data/v68.0/queryAll/?q=SELECT+Id,Name,Amount,StageName,CloseDate+FROM+Opportunity';

        $params = [
            'grant_type' => config('services.salesforce.grant_type'),
            'client_id' => config('services.salesforce.client_id'),
            'client_secret' => config('services.salesforce.client_secret')
        ];

        $api = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Accept' => 'application/json',
            ])
            ->asForm()
            ->post($url, $params);

        $result = $api->json();

        return $result['access_token'];
    }

    public function setToken(string $token)
    {
        session('sf_oauth_token', $token);
    }

    public function getToken()
    {
        if(session('sf_oauth_token')){
            return session('sf_oauth_token');
        }

        $token = $this->createToken();

        session('sf_oauth_token', $token);

        return $token;
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

    public function getAccounts(): Response
    {
        $url = $this->apiUrl . '/services/data/v68.0/queryAll/?q=SELECT+Id,Name+FROM+Account';

        $api = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->get($url);

        return $api;
    }

    public function getContacts(): Response
    {
        $url = $this->apiUrl . '/services/data/v68.0/queryAll/?q=SELECT+Id,FirstName,LastName+FROM+Contact';

        $api = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->get($url);

        return $api;
    }

}
