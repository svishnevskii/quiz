<?php

namespace Components\Tests\Events;

use Components\Tests\Contracts\Models\TestCounterInterface;
use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Support\Collection;
use Request;

class TestStart
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $url;

    public function __construct(string $url)
    {
        $this->url = $url;
        \Log::debug('test start event, id: ' . $url);
    }

    public function getUrl(): string
    {
        return $this->url;
    }
}
