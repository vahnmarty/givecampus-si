<?php

namespace App\Console\Commands;

use App\Services\SalesforceService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

#[Signature('salesforce:op')]
#[Description('Fetch Salesforce opportunities and save them to JSON')]
class GetOpportunities extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sf = app(SalesforceService::class);

        $this->info("Salesforce API...");

        $api = $sf->getOpportunities();

        if ($api->failed()) {
            $this->fail('Failed to fetch opportunities from Salesforce.');
        }

        $json = $api->json();

        $file = database_path('json/op.json');

        File::ensureDirectoryExists(dirname($file));

        File::put(
            $file,
            json_encode(
                $json,
                JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR
            )
        );

        $this->info("Opportunities saved to: {$file}");

    }
}
