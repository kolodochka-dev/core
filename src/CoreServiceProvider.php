<?php

namespace LindenCMS\Core\Laravel;

use Illuminate\Support\ServiceProvider;
use LindenCMS\Core\Services\Init;
use LindenCMS\Core\Contracts\InitContract;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(InitContract::class, fn($app) => new Init);
    }

    public function boot(): void
    {
        //
    }
}