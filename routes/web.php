<?php

Route::group([
    'prefix'     => 'tests',
    'middleware' => 'web',
    'namespace'  => 'Components\Tests\Controllers',
], function () {

    Route::get('/', [
        'as'   => 'get.tests.index',
        'uses' => 'TestsController@index',
    ]);

    Route::group([
        'prefix' => '{url}',
    ], function () {
        Route::get('/', [
            'as'   => 'get.tests.single',
            'uses' => 'TestsController@single',
        ]);
        Route::post('/', [
            'as'   => 'post.tests.single',
            'uses' => 'TestsController@answer',
        ]);
    });

    Route::group([
        'prefix' => 'widget/list',
    ], function () {
        Route::paginate('{url}', [
            'as'   => 'tests.widget.list',
            'uses' => 'WidgetConfigController@list',
        ]);
    });
});
