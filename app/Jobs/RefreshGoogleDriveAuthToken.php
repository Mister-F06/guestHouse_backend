<?php

namespace App\Jobs;

use Google\Service\Drive\Drive;
use Google_Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class RefreshGoogleDriveAuthToken implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $client = new Google_Client();
            $client->setClientId(config('filesystems.disks.google.clientId'));
            $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
            $client->addScope(Drive::class);
            $client->setRedirectUri(config('app.url'));
            $client->setAccessType('offline');
            $refreshToken = $client->refreshToken(config('filesystems.disks.google.refreshToken'));
            
            // Add new value
            putenv('GOOGLE_DRIVE_REFRESH_TOKEN='.$refreshToken['refresh_token']);

            // Persist the new value
            Artisan::call('config:clear');
            Artisan::call('config:cache');
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
        }
    }
}
