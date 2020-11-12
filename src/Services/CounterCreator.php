<?php

namespace Components\Tests\Services;

use Components\Tests\Contracts\Models\TestCounterInterface;

class CounterCreator
{
    private $counter;
    private $url;

    public function __construct(string $url)
    {
        $this->setCounter($this->getCounterByUrl($url) ?? $this->createCounter($url));
    }

    public function getCounter(): TestCounterInterface
    {
        return $this->counter;
    }

    private function setCounter(TestCounterInterface $counter): void
    {
        $this->counter = $counter;
    }

    private function getCounterByUrl(string $url): ?TestCounterInterface
    {
        return app()->make(TestCounterInterface::class)
            ->where('url', $url)
            ->orderBy('id', 'desc')
            ->first()
        ;
    }

    private function createCounter(string $url): TestCounterInterface
    {
        $counter = app()->make(TestCounterInterface::class);
        $counter->start = 0;
        $counter->finish = 0;
        $counter->url = $url;

        return $counter;
    }
}
