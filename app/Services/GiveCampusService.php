<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class GiveCampusService
{
    private string $apiUrl;
    private string $apiKey;

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

    public function getGifts()
    {
        $url = $this->apiUrl . '/gifts';

        $api =  Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->get($url);

        return $api->json();
    }

    public function getRequest(string $request_id)
    {
        $url = $this->apiUrl . '/results/' . $request_id;

        $api = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Accept' => 'application/json',
        ])->get($url);

        $json =  $api->json();

        if($json['status'] === 'completed') {

            $download_url = $json['download_url'];

            if($download_url) {
                $data = $this->getDownloadUrl($download_url);

                return $data;
            }


        }

        return $json;
    }

    public function getDownloadUrl(string $url)
    {
        $api = Http::get($url);

        return $api->json();
    }
}
