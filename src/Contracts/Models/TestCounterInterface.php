<?php

namespace Components\Tests\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;

interface TestCounterInterface
{
    //relations
    public function test(): Relation;

    //scopes
    public function scopeByUrl(Builder $query, string $url): Builder;
}
