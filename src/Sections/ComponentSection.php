<?php

namespace Components\Tests\Sections;

use AdminColumn;
use AdminColumnEditable;
use AdminDisplay;
use AdminForm;
use AdminFormElement;
use AdminNavigation;
use Components\Tests\Models\Template;
use Components\Tests\Models\RenderMethod;
use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Section;
use App\Lang;

/**
 * Class CategoriesComponentSection
 *
 * @property \Components\Categories\Models\MagazineComponent $model
 *
 * @see http://sleepingowladmin.ru/docs/model_configuration_section
 */
class ComponentSection extends Section
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
    protected $title = 'Компоненты';

    /**
     * @var string
     */
    protected $alias = 'test_components';

    /**
     * @return DisplayInterface
     */
    public function onDisplay()
    {
        $tableAsync = AdminDisplay::table()
            ->setApply(function($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->with([
                'widget',
            ])
            ->setHtmlAttribute('class', 'table-primary')
            ->setColumns([
                AdminColumn::text('id', '№')->setHtmlAttribute('class', 'reorder'),
                AdminColumn::link('name', 'Имя'),
                AdminColumn::text('template.name', 'Название шаблона'),
                AdminColumnEditable::text('count', 'кол-во'),
            ])->paginate(25);

        return $tableAsync;
    }

    /**
     * @param int $id
     *
     * @return FormInterface
     */
    public function onEdit($id)
    {
        $config = config('component.admin');

        $form = AdminForm::panel()->addBody([
            AdminFormElement::text('name', 'Имя компонента')->required(),

            AdminFormElement::columns()->addColumn(['<hr>'], 12),

            AdminFormElement::columns()
                ->addColumn([
                    AdminFormElement::select('template_id', 'Шаблон вывода новости', Template::class)
                        ->setHtmlAttribute('placeholder', 'Выберите Шаблон вывода новости')
                        ->setDisplay('name')
                        ->required()
                    ,
                ], 4)
                ->addColumn([
                    AdminFormElement::select('method_id', 'Специальный метод сбора новостей (?)', RenderMethod::class)
                        ->setHtmlAttribute('placeholder', 'Выберите метод сбора новостей')
                        ->setHelpText('Метод в классе для сбора специфичных запросов')
                        ->setDisplay('name'),
                ], 4)
                ->addColumn([
                    AdminFormElement::select('lang_id', 'Язык', Lang::class)
                        ->setDisplay('name')
                        ->setHtmlAttribute('placeholder', 'Выберите язык')
                        ->setDefaultValue(Lang::defaultLang())
                        ->required()
                    ,
                ], 4)
            ,

            AdminFormElement::columns()->addColumn(['<hr>'], 12),

            AdminFormElement::number('count', 'Количество связанных элементов'),
        ]);

        return $form;
        // remove if unused
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
