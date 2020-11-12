<?php

namespace Components\Tests\Models;

use App\Traits\LangTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use SleepingOwl\Admin\Traits\OrderableModel;

class TestAnswer extends Model
{
    use SoftDeletes;
    use LangTrait;
    use OrderableModel;

    protected $table = 'test_answers';

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function question()
    {
        return $this->belongsTo(TestQuestion::class);
    }
}
