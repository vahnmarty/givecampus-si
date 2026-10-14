<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GiveCampusService
{
    private string $apiUrl;
    private string $apiKey;
    private int $startDate;
    private int $endDate;

    public function __construct()
    {
        $this->apiUrl = config('services.givecampus.url');
        $this->apiKey = config('services.givecampus.key');
    }

    public function getApiUrl(): string
    {
        return $this->apiUrl;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function setStartDate(int $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function setEndDate(int $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getGifts()
    {
        $url = $this->apiUrl . '/gifts';

        $params = [
            'start' => $this->startDate,
            'end' => $this->endDate,
            'time_field' => 'updated_at',
            'authorized' => 'true',
            'charged_back' => 'true',
            'disputed' => 'true',
            'failed' => 'true',
            'paid' => 'true',
            'pending' => 'true',
            'pending_authorization' => 'true',
            'refunded' => 'true',
        ];

        $api =  Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
            ])->get($url, $params);

        $fullUrl = $url . '?' . http_build_query($params);

        Log::info("GiveCampus API Request URL: {$fullUrl}");

        return $api->json();
    }

    public function getRequest(string $request_id)
    {
        $url = $this->apiUrl . '/results/' . $request_id;

        $api = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->get($url);

        return $api->json();

    }


    public function getDownloadUrl(string $url)
    {
        $api = Http::get($url);

        return $api->json();
    }
}
