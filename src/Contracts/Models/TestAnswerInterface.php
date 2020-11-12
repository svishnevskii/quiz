<?php

namespace Components\Tests\Contracts\Models;

use Illuminate\Database\Eloquent\Relations\Relation;

interface TestAnswerInterface
{
    //relations
    public function question(): Relation;
}
