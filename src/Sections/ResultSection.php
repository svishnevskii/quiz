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
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Contracts\Display\Extension\FilterInterface;
use SleepingOwl\Admin\Section;

/**
 * Class TestResultSection
 *
 * @property \Components\Categories\Models\TestResult $model
 *
 * @see http://sleepingowladmin.ru/docs/model_configuration_section
 */
class ResultSection extends Section
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
    protected $title = 'Результаты';

    /**
     * @var string
     */
    protected $alias = 'test_results';

    /**
     * @return DisplayInterface
     */
    public function onDisplay()
    {
        $table = AdminDisplay::datatablesAsync()
            ->setApply(function ($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->with('test')
            ->setHtmlAttribute('class', 'table-primary')
            ->setColumns([
                AdminColumn::text('id', '№')->setWidth('50px'),
                AdminColumn::text('text', 'Текст'),
                AdminColumn::text('min_wieght', 'Мин. значение'),
                AdminColumn::text('max_wieght', 'Макс. значение'),
                //AdminColumn::text('is_right', 'Правильный результат'),
                AdminColumn::text('test.title', 'Тест')
                    ->setOrderable(false)
                    ->append(
                        AdminColumn::filter('test_id')
                    ),
            ])
            ->paginate(20);

        $table->setColumnFilters([
            AdminColumnFilter::text()->setPlaceholder('№')->setOperator(FilterInterface::EQUAL),
            AdminColumnFilter::text()->setPlaceholder('Заголовок')->setOperator(FilterInterface::CONTAINS),
            null,
            null,
            AdminColumnFilter::select(new Test, 'Title')->setDisplay('title')->setPlaceholder('Выберите тест')->setColumnName('test_id'),
        ])
        ;

        $table->setFilters(
            AdminDisplayFilter::related('test_id')->setModel(Test::class)
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
            AdminFormElement::select('test_id', 'Тест', Test::pluck('title', 'id')->all())
                ->setDisplay('title')
                ->setHtmlAttribute('placeholder', 'Выберите Тест')
                ->required(),
            AdminFormElement::textarea('text', 'Текст')->required(),
            AdminFormElement::select('lang_id', 'Язык', Lang::class)
                ->setDisplay('name')
                ->setHtmlAttribute('placeholder', 'Выберите язык')
                ->setDefaultValue(Lang::defaultLang())
                ->required(),
            AdminFormElement::number('min_wieght', 'Мин. значение')->required(),
            AdminFormElement::number('max_wieght', 'Макс. значение')->required(),
            //AdminFormElement::checkbox('is_right', 'Правильный результат')->setDefaultValue(0),
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
