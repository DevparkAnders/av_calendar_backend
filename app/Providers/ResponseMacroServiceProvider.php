<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Response;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        // register API response
        Response::macro('api',
            function ($data, $status = 200, array $headers = []) {

                $output['response'] = $data;
                $output['exec_time'] = defined('LARAVEL_START') ?
                    round(microtime(true) - LARAVEL_START, 4) : 0;
                
                return Response::make($output, $status,
                    $headers);
            });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
