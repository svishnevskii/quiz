<?php

namespace Components\Tests\Controllers;

use App\Http\Controllers\Controller;
use App\WidgetConfig;
use Cache;
use Components\Tests\Models\Test;
use Carbon\Carbon;

class WidgetController extends Controller
{
    protected $model;
    protected $widget;
    protected $config;

    public function __construct(WidgetConfig $widgetConfig)
    {
        $this->model  = new Test();
        $this->widget = $widgetConfig->widgetable;
        $this->config = $widgetConfig;
    }

    public function list()
    {
        $this->filter();
        $paginate            = $this->model->simplePaginate();
        $this->widget->count = null;

        return $paginate;
    }

    public function query()
    {
        $config        = $this->config;
        $componentName = $this->config->name;
        $modelAll      = Cache::remember(
            \App::getLocale() . ':' .
            config('cache.stores.redis.prefix') .
            ':tests_component:' . $this->widget->id .
            ':' . str_slug($this->widget->name),
            config('cache.stores.redis.ttl'),
            function () {
                $this->filter();

                return $this->model->get();
            }
        );

        if ($modelAll->isEmpty()) return null;

        $view = view('modules.tests.widgets.' . $this->widget->template->view, compact(
            [
                'modelAll',
                'componentName',
                'config',
            ]
        ))->render();
        
        return $view;
    }

    private function filter()
    {
        $this->published();
        $this->orderBy();
        $this->with();
    }

    protected function published()
    {
        $this->model = $this->model->where('published', true);
        $this->model = $this->model->where('public_start', '<=', Carbon::now());
    }

    protected function with()
    {
        $this->model = $this->model->with([
            'questions' => function ($query) {
                $query->where('published', true);
            },
            'questions.answers',
        ]);
    }

    protected function limit()
    {
        if (!is_null($this->widget->count)) {
            $this->model = $this->model->limit($this->widget->count);
        }
    }

    protected function orderBy()
    {
        $this->model = $this->model->orderBy('public_start', 'desc');
    }
}
