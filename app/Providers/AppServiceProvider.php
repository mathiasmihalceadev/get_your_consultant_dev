<?php

namespace App\Providers;

use App\Queue\CompatibilityWorker;
use App\Services\OpenAIService;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(OpenAIService::class);
    }

    public function boot(): void
    {
        $this->app->extend('queue.worker', function ($worker, $app) {
            $isDownForMaintenance = fn () => $app->isDownForMaintenance();

            $resetScope = function () use ($app) {
                if (method_exists($app['log'], 'flushSharedContext')) {
                    $app['log']->flushSharedContext();
                }

                if (method_exists($app['log'], 'withoutContext')) {
                    $app['log']->withoutContext();
                }

                if (method_exists($app['db'], 'getConnections')) {
                    foreach ($app['db']->getConnections() as $connection) {
                        $connection->resetTotalQueryDuration();
                        $connection->allowQueryDurationHandlersToRunAgain();
                    }
                }

                $app->forgetScopedInstances();

                Facade::clearResolvedInstances();

                memory_reset_peak_usage();
            };

            return new CompatibilityWorker(
                $app['queue'],
                $app['events'],
                $app[ExceptionHandler::class],
                $isDownForMaintenance,
                $resetScope,
            );
        });

        Vite::prefetch(concurrency: 3);
    }
}
