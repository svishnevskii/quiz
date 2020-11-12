<?php

namespace Components\Tests\Services;

use App\Scope\LangScope;
use Cache;
use Carbon\Carbon;
use Components\Tests\Objects\ResultResponse;
use Components\Tests\Contracts\Models\TestAnswerInterface;
use Components\Tests\Contracts\Models\TestResultInterface;
use Components\Tests\Events\TestStart;
use Components\Tests\Events\TestFinish;

//TODO decomposiete
class ResultResponseFacade
{
    private $response;
    private $url;
    private $questionAnswer;
    private $answersStore;

    public function __construct(
        ResultResponse $response,
        string $url,
        array $questionAnswer,
        array $answersStore
    )
    {
        $this->response = $response;
        $this->url = $url;
        $this->questionAnswer = $questionAnswer;
        $this->answersStore = $answersStore;
    }

    public function make(): array
    {
        if (1 === count($this->answersStore)) {
            event(new TestStart($this->url));
        }

        if (0 === $this->getLeftQuestions()) {
            $this->setFinished();
        }

        return $this->getResult();
    }

    public function getResponse(): ResultResponse
    {
        return $this->response;
    }

    public function getAnswersStore(): array
    {
        return $this->answersStore;
    }

    private function setFinished(): void
    {
        $testPoints = $this->getTestPoints();
        $testResult = $this->getTestResult($testPoints);

        $this->response->setIsDone(! is_null($testResult));

        if ($this->response->getIsDone()) {
            $this->response->setText($testResult->text);
            $this->response->setPoints(sprintf('%s/%s', $testPoints, $this->questionAnswer['questions_count']));

            // set test finished
            event(new TestFinish($this->url));
        }
    }

    private function getTestPoints(): int
    {
        return app()->make(TestAnswerInterface::class)
            ->whereIn('id', array_values($this->answersStore))
            ->sum('wieght')
        ;
    }

    private function getTestResult(int $testPoints): ?TestResultInterface
    {
        return Cache::remember(
            \App::getLocale() . ':' .
            config('cache.stores.redis.prefix') . ':tests_result:' . $this->url . ':' . $testPoints,
            config('cache.stores.redis.ttl'),
            function () use ($testPoints) {
                $model = app()->make(TestResultInterface::class)
                    ->where('min_wieght', '<=', $testPoints)
                    ->where('max_wieght', '>=', $testPoints)
                    ->whereHas('test', function ($query) {
                        $query->where('published', true)
                            ->withoutGlobalScope(LangScope::class)
                            ->public(Carbon::now())
                            ->where('url', $this->url)
                        ;
                    })
                    ->first();
                return $model;
            }
        );
    }

    private function getLeftQuestions(): int
    {
        return $this->questionAnswer['questions_count'] - count($this->answersStore);
    }

    private function getResult(): array
    {
        return [
            'is_done' => $this->response->getIsDone(),
            'points' => $this->response->getPoints(),
            'result' => $this->response->getText(),
            'right_aid' => $this->response->getRightAnswerId(),
        ];
    }
}
