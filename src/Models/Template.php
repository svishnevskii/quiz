<?php

namespace Components\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Template extends Model
{
    protected $table = 'test_templates';

    use SoftDeletes;

    public function component()
    {
        return $this->hasMany(TestComponent::class);
    }
}
