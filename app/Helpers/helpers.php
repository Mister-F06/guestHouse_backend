<?php

use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

/**
 * Generate temporary url with signature token and expires times
 *
 * @param string $email , $route
 * @return string
*/
if (!function_exists('generateSignedRoute')) {
    function generateSignedRoute($email , $mode = 'email.verify'){
        $token = Str::random(40);

        $email_data = [
            'url' => URL::temporarySignedRoute(
                $mode,
                now()->addHour(),
                ['token' => $token]
            ),
            'site_url' => config('app.front_url')
        ];

        return ['email_data' => $email_data, 'token' => $token];
    }
}



/**
 * Update signed url with custom front url
 *
 * @param string $email , $route
 * @return string
*/
if (!function_exists('updateSignedLink')) {
    function updateSignedLink($link , $route , $front_url) {
        return str_replace($route , config('app.front_url').''.$front_url , $link);
    }
}