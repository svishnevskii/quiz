<?php

namespace Components\Tests\Contracts\Models;

use App\Interfaces\ModelBaseInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;

interface TestInterface
{
    //relations
    public function questions(): Relation;
    public function results(): Relation;

    //mixins
    public function setUrlAttribute($value): void;
    public function getFullUrlAttribute(): string;
    public function getPathNameAttribute();

    //scopes
    public function scopePublic($query, Carbon $now): Builder;
}
