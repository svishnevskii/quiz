<?php

namespace Components\Tests\Controllers;

use App\Http\Controllers\Controller;
use App\Scope\LangScope;
use App\WidgetConfig;

class WidgetConfigController extends Controller
{
    public function list($url)
    {
        $config = WidgetConfig::where('url', $url)
            ->withoutGlobalScope(LangScope::class)
            ->first();

        if (null === $config) {
            return abort(404);
        }

        $paginate = $config->widgetable->list($config);

        return view('modules.tests.list.index', [
            'config'    => $config,
            'title'     => $config->widgetable->name,
            'paginator' => $paginate,
        ]);
    }
}
