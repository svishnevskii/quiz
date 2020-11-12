<?php

namespace Components\Tests\Contracts\Models;

use App\Interfaces\ModelBaseInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\Builder;

interface TestQuestionInterface
{
    //relations
    public function test(): Relation;
    public function answers(): Relation;
}
