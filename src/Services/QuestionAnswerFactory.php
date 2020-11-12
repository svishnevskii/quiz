<?php

namespace Components\Tests\Services;

use Components\Tests\Contracts\Models\TestInterface;
use Carbon\Carbon;
use Components\Tests\Models\Test;

class QuestionAnswerFactory
{
    public static function get($value): array
    {
        $qa = [];

        $factory = new static();
        if (is_string($value)) {
            $qa = $factory->getByUrl($value);
        } elseif ($value instanceof TestInterface) {
            $qa = $factory->getByTestModel($value);
        }

        return $qa;
    }

    private function getByTestModel(TestInterface $model): array
    {
        $rightQA = [];

        foreach ($model->questions as $question) {
            $answer = $question->answers
                ->filter(function ($answer) {
                    return $answer->wieght > 0;
                })
                ->first()
            ;

            if (is_null($answer)) {
                continue;
            }

            $rightQA = $this->addAnswer(
                $rightQA,
                $question->id,
                $this->makeAnswer($answer->id, $answer->text)
            );
        }

        return $this->makeQuestionAnswer($rightQA);
    }

    private function getByUrl(string $url): array
    {

        $testId = Test::where('url',$url)->first()->id;

        $questionAnswers = \DB::table('test_questions')
            ->select(
                'test_questions.id as question_id',
                'test_answers.id as answer_id',
                'test_answers.text as text'
            )
            ->leftJoin('test_answers', 'test_questions.id', '=', 'test_answers.question_id')
            ->join('tests', 'test_questions.test_id', '=', 'tests.id')
            ->where('tests.published', 1)
            ->where('tests.id', $testId)
            ->where('test_questions.published', 1)
            ->where(function ($query) {
                $query->where('tests.public_start', '<', Carbon::now())
                    ->orWhereNull('tests.public_start');
            })
            ->where('test_answers.wieght', '>', 0)
            ->get()
        ;

        $rightQA = [];

        foreach ($questionAnswers as $qa) {
            $rightQA = $this->addAnswer(
                $rightQA,
                $qa->question_id,
                $this->makeAnswer($qa->answer_id, $qa->text)
            );
        }

        return $this->makeQuestionAnswer($rightQA);
    }

    private function addAnswer(array $qa, int $question_id, array $answer): array
    {
        $qa[$question_id] = $answer;

        return $qa;
    }

    private function makeAnswer($id, $text): array
    {
        return [
            'answer_id' => $id,
            'text' => $text,
        ];
    }

    private function makeQuestionAnswer($questionAnswer): array
    {
        return [
            'questions_count' => count($questionAnswer),
            'question_answer' => $questionAnswer,
        ];
    }
}
