<?php

namespace Components\Tests\Models;

use App\Traits\LangTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use SleepingOwl\Admin\Traits\OrderableModel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Components\Tests\Contracts\Models\TestQuestionInterface;

class TestQuestion extends Model implements TestQuestionInterface
{
    use SoftDeletes;
    use LangTrait;
    use OrderableModel;

    protected $table = 'test_questions';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function test(): Relation
    {
        return $this->belongsTo(Test::class);
    }

    public function answers(): Relation
    {
        return $this->hasMany(TestAnswer::class, 'question_id');
    }
}
