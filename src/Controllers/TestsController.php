<?php

namespace Components\Tests\Controllers;

use App\Events\ViewPage;
use App\Http\Controllers\Controller;
use App\Scope\LangScope;
use App\WidgetConfig;
use Cache;
use Carbon\Carbon;
use Components\Tests\Contracts\Models\TestInterface;
use Components\Tests\Contracts\Models\TestAnswerInterface;
use Components\Tests\Contracts\Models\TestResultInterface;
use Components\Tests\Services\ResultResponseFacade;
use Components\Tests\Objects\ResultResponse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Validator;
use Components\Tests\Services\QuestionAnswerFactory;

class TestsController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

    public function index(Request $request)
    {
        $page = strpos($request->path(), 'page') !== false ? $request->segment(3) : 1;

        $collection = Cache::remember(
            \App::getLocale() . ':' .
            config('cache.stores.redis.prefix') . ':tests_index_' . $page,
            config('cache.stores.redis.ttl'),
            function () {
                return app()->make(TestInterface::class)
                    ->public(Carbon::now())
                    ->orderByDesc('public_start')
                    ->simplePaginate()
                ;
            }
        );

        return view('modules.tests.list.index', [
            'title'  => \Lang::trans('tests.main.list.title'),
            'paginator' => $collection,
        ]);
    }

    public function single(Request $request, $url)
    {
        $model = Cache::remember(
            \App::getLocale() . ':' .
            config('cache.stores.redis.prefix') . ':tests_page:' . $url,
            config('cache.stores.redis.ttl'),
            function () use ($url) {
                $model = app()->make(TestInterface::class)
                    ->where('url', $url)
                    ->where('published', true)
                    ->withoutGlobalScope(LangScope::class)
                    ->public(Carbon::now())
                    // ->whereHas('questions', function ($query) {
                    //     $query->where('published', true);
                    // })
                    ->with([
                        'questions' => function ($query) {
                            $query->orderBy('order');
                        },
                        'questions.answers' => function ($query) {
                            $query->orderBy('order');
                        },
                    ])
                    ->first();

                return $model;
            }
        );

        // add questions with answer to cache
        $this->getCachedQA($model, $model->url);

        // register selected answers on session
        $request->session()->put('test_page:' . $url, []);

        return response()->view('modules.tests.page.single', [
            'model' => $model,
        ]);
    }

    public function answer(Request $request, $url)
    {
        \Debugbar::disable();

        $isExistsTest = $request->session()->has('test_page:' . $url);

        if (! $isExistsTest) {
            return response()->json('Вы еще не начали этот тест, либо он отстутствует', 404);
        }

        $validator = Validator::make($request->all(), [
            'qid' => 'integer|required|min:1',
            'aid' => 'integer|required|min:1',
        ]);

        if ($validator->fails()) {
            return $validator->errors()->all();
        }

        $questionAnswer = $this->getCachedQA($url, $url);
        $answersStore = $request->session()->get('test_page:' . $url);
        $answersStore[$request->input('qid')] = $request->input('aid');

        if (! isset($questionAnswer['question_answer'][$request->input('qid')])) {
            return response()->json('Ошибка, вопрос отстутствует', 404);
        }

        $resultResponse = new ResultResponse();
        $resultResponse->setRightAnswerId(
            $questionAnswer['question_answer'][$request->input('qid')]['answer_id']
        );

        $responseFacade = new ResultResponseFacade(
            $resultResponse,
            $url,
            $questionAnswer,
            $answersStore
        );

        $request->session()->put('test_page:' . $url, $responseFacade->getAnswersStore());

        $response = $responseFacade->make();

        if ($responseFacade->getResponse()->getIsDone()) {
            $request->session()->forget('test_page:' . $url);
        }

        return $response;
    }

    private function getCachedQA($value, string $key): array
    {
        return Cache::remember(
            \App::getLocale() . ':' .
            config('cache.stores.redis.prefix') . ':tests_page_qa:' . $key,
            config('cache.stores.redis.ttl'),
            function () use ($value) {
                return QuestionAnswerFactory::get($value);
            }
        );
    }

    private function notExistsModelExcepion($model)
    {
        if (is_null($model)) {
            \App::abort(404, 'Выпуск отсутствует');
        }
    }
}
