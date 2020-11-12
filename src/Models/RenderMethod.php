<?php

namespace Components\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RenderMethod extends Model
{
    protected $table = 'test_render_methods';

    use SoftDeletes;

    public function component()
    {
        return $this->hasMany(TestComponent::class, 'method_id');
    }
}
