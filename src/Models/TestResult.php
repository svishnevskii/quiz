<?php

namespace Components\Tests\Models;

use App\Traits\LangTrait;
use Components\Tests\Contracts\Models\TestResultInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestResult extends Model implements TestResultInterface
{
    use SoftDeletes;
    use LangTrait;

    protected $table = 'test_results';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function test(): Relation
    {
        return $this->belongsTo(Test::class);
    }
}
