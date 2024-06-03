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
use Illuminate\Support\Facades\Http;
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
            // $client = new Google_Client();
            // $client->setClientId(config('filesystems.disks.google.clientId'));
            // $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
            // $client->addScope(Drive::class);
            // $client->setRedirectUri(config('app.url'));
            // $client->setAccessType('offline');
            // $refreshToken = $client->refreshToken(config('filesystems.disks.google.refreshToken'));

            // Remplacez les valeurs placeholders par vos propres informations
            $clientId = config('filesystems.disks.google.clientId');
            $clientSecret = config('filesystems.disks.google.clientSecret');
            $refreshToken = config('filesystems.disks.google.refreshToken');
            
            $response = Http::post('https://oauth2.googleapis.com/token', [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'refresh_token' => $refreshToken,
                'grant_type' => 'refresh_token',
            ]);


            // Vérifiez si la requête a réussi
            if ($response->successful()) {
                $accessToken = $response['access_token'];
                Log::info($accessToken);
                // Utilisez le nouvel access token dans votre application
                // ...
            } else {
                // Gérez les erreurs (par exemple, journalisez-les)
                $error = $response->json();
                Log::info($error);
            }
            Log::info($response->status());
            // // Add new value
            // putenv('GOOGLE_DRIVE_REFRESH_TOKEN='.$refreshToken['refresh_token']);

            // // Persist the new value
            // Artisan::call('config:clear');
            // Artisan::call('config:cache');
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
        }
    }
}
