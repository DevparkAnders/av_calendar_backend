<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Logging\Log;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;

class LogServiceProvider extends ServiceProvider
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Log
     */
    protected $log;

    /**
     * LogServiceProvider constructor.
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->request = $app['request'];
        $this->log = $app['log'];
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $auth = $this->app['auth'];

        /** @var \Monolog\Logger $monolog */
        $monolog = $this->log->getMonolog();
        $monolog->pushProcessor(function ($record) use ($auth) {

            $record['extra'] =
                [
                    'user' => [
                        'id' => $auth->check() ? $auth->user()->id : 0,
                        'ip' => $this->request->getClientIp(),
                    ],
                ];

            // try to get artisan command
            $command = $this->request->server('argv', null);

            // if artisan command - include it in log
            if ($command !== null) {
                if (is_array($command)) {
                    $command = implode(' ', $command);
                }
                $record['extra']['command'] = $command;
            } else {
                // if via HTTP - add HTTP data
                $record['extra']['request'] = [
                    'url' => $this->request->fullUrl(),
                    'method' => $this->request->method(),
                    'input' => $this->request->all(),
                ];
            }

            return $record;
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}
