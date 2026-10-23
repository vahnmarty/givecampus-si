<?php
namespace App\Console\Commands;

use App\Services\GiveCampusService;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('givecampus:gifts')]
#[Description('/gifts API')]
class GetGifts extends Command
{
    public function handle(): void
    {
        $date_from = Carbon::parse('2026-10-01 00:00:00')->unix();
        $date_to = Carbon::parse('2026-12-31 23:59:59')->unix();

        $this->info("Unix Start Date: {$date_from}");
        $this->info("Unix End Date: {$date_to}");

        $download_url = null;

        $givecampus = app(GiveCampusService::class);

        $givecampus->setStartDate($date_from);
        $givecampus->setEndDate($date_to);

        // Initiate the gifts request.
        $response = $givecampus->getGifts();



        $requestId = $response['request_id'] ?? null;

        if (! $requestId) {
            Log::error("API Error", $response->json());
            $this->fail('Failed to start the gifts request.');
        }

        $attempt = 0;
        $maxAttempts = 4;
        $status = $response['status'] ?? null;

        $this->info("Status: {$status}. Request ID: {$requestId}");

        // Poll while the request is still processing.
        do {
            if ($status === 'completed') {
                break;
            }

            if ($status !== 'in_progress') {
                $this->fail("Request failed. Status: {$status}");
            }

            if (++$attempt > $maxAttempts) {
                $this->fail('Gifts request timed out.');
            }

            $this->info("Request in progress. Attempt {$attempt}/{$maxAttempts}...");

            $response = $givecampus->getRequest($requestId);

            $status = $response['status'] ?? null;

        } while ($status === 'in_progress');

        if ($status !== 'completed') {
            $this->fail("Request did not complete. Status: {$status}");
        }

        $download_url = $response['download_url'] ?? null;

        if (! $download_url) {
            $this->fail('No download URL found in the response.');
        }

        $givecampus->getDownloadUrl($download_url);

        $this->info('Gifts request completed.');

    }
}
