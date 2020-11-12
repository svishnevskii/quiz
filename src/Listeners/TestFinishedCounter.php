<?php

namespace Components\Tests\Listeners;

use Components\Tests\Events\TestFinish;
use Components\Tests\Services\CounterCreator;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Log;

class TestFinishedCounter implements ShouldQueue
{
    public function handle(TestFinish $event)
    {
        $creator = new CounterCreator($event->getUrl());
        $counter = $creator->getCounter();
        $counter->finish += 1;

        if ($this->isValidate($counter)) {
            $counter->save();
            \Log::debug('test finidshed, id: ' . $counter->id);
        }
    }

    private function isValidate($counter): bool
    {
        return $counter->finish <= $counter->start;
    }
}
