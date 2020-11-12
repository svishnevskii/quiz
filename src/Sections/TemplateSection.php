<?php

namespace Components\Tests\Sections;

use AdminColumn;
use AdminDisplay;
use AdminForm;
use AdminFormElement;
use AdminNavigation;
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Section;

/**
 * Class TemplateSection
 *
 * @property \Components\Categories\Models\Template $model
 *
 * @see http://sleepingowladmin.ru/docs/model_configuration_section
 */
class TemplateSection extends Section
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
    protected $title = 'Шаблоны';

    /**
     * @var string
     */
    protected $alias = 'test_templates';

    /**
     * @return DisplayInterface
     */
    public function onDisplay()
    {
        $table = AdminDisplay::table()
            ->setApply(function($query) {
                $query->orderBy('created_at', 'desc');
            })
            ->setHtmlAttribute('class', 'table-primary')
            ->setColumns([
                AdminColumn::text('id', '№'),
                AdminColumn::text('name', 'Название шаблона'),
                AdminColumn::text('view', 'файл шаблона'),
            ])
            ->paginate(20);

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
            AdminFormElement::text('name', 'Название шаблона')->required(),
            AdminFormElement::text('view', 'файл шаблона (без .blade.php)')->required(),
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
