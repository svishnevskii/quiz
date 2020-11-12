<?php

namespace Components\Tests;

use App\Providers\ModuleServiceProviderAbstract;
use Components\Tests\Models\Test;
use Components\Tests\Observers\TestObserver;
use Components\Tests\Providers\EventServiceProvider;

class TestsServiceProvider extends ModuleServiceProviderAbstract
{
    protected function initPublishes(): void
    {
        $this->publishes([
            __DIR__ . '/../config/' => config_path(),
        ], 'config');
    }

    protected function initMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../database/');
    }

    protected function loadRoutes(): void
    {
        include __DIR__ . '/../routes/web.php';
    }

    protected function initObservers(): void
    {

    }

    public function register()
    {
        // Test
        $this->app->bind(
            \Components\Tests\Contracts\Models\TestInterface::class,
            \Components\Tests\Models\Test::class
        );

        // Question
        $this->app->bind(
            \Components\Tests\Contracts\Models\TestQuestionInterface::class,
            \Components\Tests\Models\TestQuestion::class
        );

        // Answer
        $this->app->bind(
            \Components\Tests\Contracts\Models\TestAnswerInterface::class,
            \Components\Tests\Models\TestAnswer::class
        );

        // Result
        $this->app->bind(
            \Components\Tests\Contracts\Models\TestResultInterface::class,
            \Components\Tests\Models\TestResult::class
        );

        // Counter
        $this->app->bind(
            \Components\Tests\Contracts\Models\TestCounterInterface::class,
            \Components\Tests\Models\TestCounter::class
        );

        $this->app->register(EventServiceProvider::class);
    }
}
