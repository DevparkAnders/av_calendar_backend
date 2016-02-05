<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->registerLocalProviders();
        }
    }

    /**
     * Register local providers that should be used only for development
     * purposes
     */
    protected function registerLocalProviders()
    {
        $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        $this->app->register(\Mnabialek\LaravelSqlLogger\Providers\ServiceProvider::class);
    }
}
