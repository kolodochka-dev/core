<?php

namespace LindenCMS\Core;

use Illuminate\Support\ServiceProvider;
use LindenCMS\Core\Services\RecursiveInit;
use LindenCMS\Core\Services\CachedInit;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
    }
}