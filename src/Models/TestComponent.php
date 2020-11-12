<?php

namespace Components\Tests\Models;

use App\Traits\LangTrait;
use App\WidgetConfig;
use Components\Tests\Controllers\WidgetController;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TestComponent extends Model
{
    protected $table = 'test_components';

    use SoftDeletes;
    use LangTrait;
    
    public function render($item)
    {
        return (new WidgetController($item))->query();
    }

    public function method()
    {
        return $this->belongsTo(RenderMethod::class);
    }

    public function list($item)
    {
        return (new WidgetController($item))->list();
    }

    public function template()
    {
        return $this->belongsTo(Template::class);
    }

    public function widget()
    {
        return $this->morphMany(WidgetConfig::class, 'widgetable');
    }
}
