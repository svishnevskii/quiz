<?php

namespace Components\Tests\Observers;

use App\Events\UpdatedModel;
use Components\Categories\Models\Category;
use Illuminate\Validation\Rule;

class TestObserver
{
    public function saving(Category $model)
    {
        $validator = \Validator::make($model->toArray(), [
            'url' => [
                'required',
                Rule::unique($model->getTable())->ignore($model->id),
            ],
        ]);

        if ($validator->fails()) {
            $model->url .= '-' . time();
        }
    }

    public function created(Category $model)
    {
    }

    public function updated(Category $model)
    {
    }
}
