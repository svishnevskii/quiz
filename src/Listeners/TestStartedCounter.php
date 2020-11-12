<?php

namespace Components\Tests\Listeners;

use Components\Tests\Events\TestStart;
use Components\Tests\Services\CounterCreator;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class TestStartedCounter implements ShouldQueue
{
    public function handle(TestStart $event)
    {
        $creator = new CounterCreator($event->getUrl());
        $counter = $creator->getCounter();
        $counter->start += 1;
        $counter->save();
        \Log::debug('test started, id: ' . $counter->id);
    }
}
