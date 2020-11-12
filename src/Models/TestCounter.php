<?php

namespace Components\Tests\Models;

use Components\Tests\Contracts\Models\TestCounterInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class TestCounter extends Model implements TestCounterInterface
{
    protected $table = 'test_counters';

    public $timestamps = false;

    public function test(): Relation
    {
        return $this->belongsTo(Test::class, 'url', 'url');
    }

    public function scopeByUrl(Builder $query, string $url): Builder
    {
        return $query->where('url', $url);
    }
}
