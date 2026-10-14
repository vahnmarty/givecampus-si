<?php
namespace App\Console\Commands;

use App\Services\GiveCampusService;
use Carbon\Carbon;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('givecampus:gifts')]
#[Description('/gifts API')]
class GetGifts extends Command
{
    public function handle(): int
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
            $this->error('Failed to start the gifts request.');

            return self::FAILURE;
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
                $this->error("Request failed. Status: {$status}");

                return self::FAILURE;
            }

            if (++$attempt > $maxAttempts) {
                $this->error('Gifts request timed out.');

                return self::FAILURE;
            }

            $this->info("Request in progress. Attempt {$attempt}/{$maxAttempts}...");

            $response = $givecampus->getRequest($requestId);

            $status = $response['status'] ?? null;

        } while ($status === 'in_progress');

        if ($status !== 'completed') {
            $this->error("Request did not complete. Status: {$status}");

            return self::FAILURE;
        }

        if($status === 'completed'){
            $download_url = $response['download_url'] ?? null;

            if (! $download_url) {
                $this->error('No download URL found in the response.');

                return self::FAILURE;
            }
        }

        if($download_url){
            $data = $givecampus->getDownloadUrl($download_url);

            $this->info('Gifts request completed.');
            $this->line(json_encode($data, JSON_PRETTY_PRINT));

            return self::SUCCESS;
        }

    }
}
