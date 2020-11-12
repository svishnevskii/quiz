<?php

namespace Components\Tests\Sections;

use AdminColumn;
use AdminColumnFilter;
use AdminDisplay;
use AdminDisplayFilter;
use AdminForm;
use AdminFormElement;
use App\Events\DownloadImg;
use Carbon\Carbon;
use AdminNavigation;
use App\Lang;
use Components\Tests\Models\Test;
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Contracts\Display\Extension\FilterInterface;
use SleepingOwl\Admin\Section;

/**
 * Class TemplateSection
 *
 * @property \Components\Categories\Models\Template $model
 *
 * @see http://sleepingowladmin.ru/docs/model_configuration_section
 */
class QuestionSection extends Section
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
    protected $title = 'Вопросы';

    /**
     * @var string
     */
    protected $alias = 'test_questions';

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
                AdminColumn::text('text', 'Вопрос'),
                AdminColumn::text('test.title', 'Тест')
                    ->setOrderable(false)
                    ->append(
                        AdminColumn::filter('test_id')
                    )
                ,
                AdminColumn::image('image', 'Изображение'),

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
            AdminFormElement::textarea('text', 'Вопрос')->required(),
            AdminFormElement::image('image', 'Изображение')
                ->setSaveCallback(function ($file, $path, $filename, $settings) use ($id) {
                    return $this->imgCustomSave($file, $path, $filename, $settings, $id);
                }),

            AdminFormElement::select('lang_id', 'Язык', Lang::class)
                ->setDisplay('name')
                ->setHtmlAttribute('placeholder', 'Выберите язык')
                ->setDefaultValue(Lang::defaultLang())
                ->required(),
            AdminFormElement::checkbox('published', 'Опубликовано')->setDefaultValue(1),
        ]);
    }

    protected function imgCustomSave($file, $path, $filename, $settings, $id)
    {
        $date = Carbon::now();

        $path = $path . '/tests/'
            . $date->year . '/'
            . $date->month . '/'
            . $date->day . '/';

        //проверяет есть ли папка и создает
        if (!file_exists($path)) {
            mkdir($path, 0775, true);
        }

        $file = $file->move($path, $filename);

        event(new DownloadImg($file->getRealPath(), $path, $filename));

        return ['path' => asset($path . $filename), 'value' => $path . $filename];
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
