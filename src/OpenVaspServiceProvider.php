<?php

declare(strict_types=1);

namespace LaravelOpenVasp;

use Illuminate\Support\ServiceProvider;
use LaravelOpenVasp\Contracts\TransferRepository;
use LaravelOpenVasp\Services\DatabaseTransferRepository;

class OpenVaspServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/openvasp.php', 'openvasp');

        $this->app->bind(TransferRepository::class, config('openvasp.repository', DatabaseTransferRepository::class));
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/openvasp.php' => config_path('openvasp.php'),
        ], 'openvasp-config');

        $this->loadRoutesFrom(__DIR__.'/../routes/openvasp.php');

        if (config('openvasp.database.enabled', true)) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }
}
