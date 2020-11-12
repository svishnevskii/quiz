<?php

namespace Components\Tests\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Components\Tests\Events\TestStart' => [
            'Components\Tests\Listeners\TestStartedCounter',
        ],

        'Components\Tests\Events\TestFinish' => [
            'Components\Tests\Listeners\TestFinishedCounter',
        ],

    ];
}
