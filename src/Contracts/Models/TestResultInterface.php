<?php

namespace Components\Tests\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\Relation;

interface TestResultInterface
{
    //relations
    public function test(): Relation;
}
