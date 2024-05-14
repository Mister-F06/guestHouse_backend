<?php

namespace App\Providers;

use Google\Service\Drive;
use Google_Client;
use Illuminate\Filesystem\Filesystem as FilesystemFilesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Masbug\Flysystem\GoogleDriveAdapter;

class GoogleDriveServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend('google' , function($app , $config){
            $client = new Google_Client();
            $client->setClientId(config('filesystems.disks.google.clientId'));
            $client->setClientSecret(config('filesystems.disks.google.clientSecret'));
            $client->refreshToken(config('filesystems.disks.google.refreshToken'));
            $service = new Drive($client);
            $adapter = new GoogleDriveAdapter($service , config('filesystems.disks.google.folderId'));
            return new FilesystemAdapter(new Filesystem($adapter , $config) , $adapter , $config);
        });
    }
}
