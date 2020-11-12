<?php

namespace Components\Tests\Sections;

use AdminColumn;
use AdminColumnFilter;
use AdminDisplay;
use AdminDisplayFilter;
use AdminForm;
use AdminFormElement;
use AdminNavigation;
use App\Lang;
use Components\Tests\Models\Test;
use Components\Tests\Models\TestQuestion;
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Contracts\Display\Extension\FilterInterface;
use SleepingOwl\Admin\Section;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TemplateSection
 *
 * @property \Components\Categories\Models\Template $model
 *
 * @see http://sleepingowladmin.ru/docs/model_configuration_section
 */
class AnswerSection extends Section
{
    /**
     * @see http://sleepingowladmin.ru/docs/model_configuration#ограничение-прав-доступа
     *
     * @var bool
     */
    protected $checkAccess = true;

    /**
     * @var string
     */
    protected $title = 'Ответы';

    /**
     * @var string
     */
    protected $alias = 'test_answers';

    /**
     * @return DisplayInterface
     */
    public function onDisplay()
    {
        $table = AdminDisplay::datatablesAsync()
            ->setApply(function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->with('question', 'question.test')
            ->setHtmlAttribute('class', 'table-primary')
            ->setColumns([
                AdminColumn::text('id', '№')->setWidth('50px'),
                AdminColumn::text('text', 'Ответ'),
                AdminColumn::text('question.text', 'Вопрос')
                    ->setOrderable(false)
                    ->append(
                        AdminColumn::filter('question_id')
                    ),
                AdminColumn::text('question.test.title', 'Тест'),
                AdminColumn::text('wieght', 'Вес')->setWidth('80px'),
                AdminColumn::order()
                    ->setLabel('Сорт.')
                    ->setHtmlAttribute('class', 'text-center')
                    ->setWidth('100px')
                ,
            ])
            ->paginate(20);

        $table->setColumnFilters([
            AdminColumnFilter::text()->setPlaceholder('№')->setOperator(FilterInterface::EQUAL),
            AdminColumnFilter::text()->setPlaceholder('Заголовок')->setOperator(FilterInterface::CONTAINS),
            AdminColumnFilter::select(new TestQuestion, 'Title')->setDisplay('text')->setPlaceholder('Выберите вопрос')->setColumnName('question_id'),
            AdminColumnFilter::select(new TestQuestion, 'Title')->setLoadOptionsQueryPreparer(function ($item, Builder $query) {
                return $query->with('test');
                })->setDisplay('test.title')->setPlaceholder('Выберите test')->setColumnName('question_id'),
            null,
        ])
        ;

        $table->setFilters(
            AdminDisplayFilter::related('question_id')->setModel(TestQuestion::class)
        );

        return $table;
    }

    /**
     * @param int $id
     *
     * @return FormInterface
     */
    public function onEdit($id)
    {
        return AdminForm::form()->setElements([
            AdminFormElement::select('question_id', 'Вопрос', TestQuestion::pluck('text', 'id')->all())
                ->setDisplay('text')
                ->setHtmlAttribute('placeholder', 'Выберите Вопрос')
                ->required(),
            AdminFormElement::textarea('text', 'Ответ')->required(),
            //AdminFormElement::number('wieght', 'Вес')->required()->setDefaultValue(0),
            AdminFormElement::checkbox('wieght', 'Правильный ответ')->setDefaultValue(0),
            AdminFormElement::select('lang_id', 'Язык', Lang::class)
                ->setDisplay('name')
                ->setHtmlAttribute('placeholder', 'Выберите язык')
                ->setDefaultValue(Lang::defaultLang())
                ->required(),
        ]);
    }

    /**
     * @return FormInterface
     */
    public function onCreate()
    {
        return $this->onEdit(null);
    }

    /**
     * @return void
     */
    public function onDelete($id)
    {
        // remove if unused
    }

    /**
     * @return void
     */
    public function onRestore($id)
    {
        // remove if unused
    }
}
