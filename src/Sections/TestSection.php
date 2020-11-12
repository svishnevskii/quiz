<?php

namespace Components\Tests\Sections;

use AdminColumn;
use AdminColumnEditable;
use AdminColumnFilter;
use AdminDisplay;
use AdminDisplayFilter;
use AdminForm;
use AdminFormElement;
use AdminNavigation;
use AdminSection;
use App;
use App\Events\DownloadImg;
use App\Lang;
use Auth;
use Carbon\Carbon;
use Components\Tests\Models\Test;
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Display\Extension\FilterInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Section;

/**
 * Class CategorySection
 *
 * @property \Components\Categories\Models\Category $model
 *
 * @see http://sleepingowladmin.ru/docs/model_configuration_section
 */
class TestSection extends Section
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
    protected $title = 'Тесты';

    /**
     * @var string
     */
    protected $alias = 'tests';

    public function __construct(\Illuminate\Contracts\Foundation\Application $app, $class)
    {
        parent::__construct($app, $class);

        $this->sizes = config('image.size');
        $this->redirect = ['edit' => 'display'];
    }

    /**
     * @return DisplayInterface
     */
    public function onDisplay()
    {
        $table = AdminDisplay::datatablesAsync()
            ->setApply(function ($query) {
                $query->orderBy('public_start', 'desc');
            })
            ->setHtmlAttribute('class', 'table-primary')
            ->setColumns([
                AdminColumn::text('id', '№')->setWidth('50px'),
                AdminColumn::link('title', 'Заголовок'),
                AdminColumn::datetime('public_start', '&#128197;')
                    ->setFormat('d-m-Y')
                    ->setWidth('150px')
                ,
                //ссылка на объект
                AdminColumn::custom('url', function (\Illuminate\Database\Eloquent\Model $model) {
                    return
                        "<a href=". url($model->full_url) . " target='&quot;_blank&quot;'>
                          <i data-toggle='tooltip' class='fa fa-arrow-circle-right' data-title='Перейти' aria-describedby='tooltip202527'></i>
                        </a>";
                }),
                AdminColumn::image('image', 'Изображение'),
            ])->paginate(20);

        return $table;
    }

    /**
     * @param int $id
     *
     * @return FormInterface
     */
    public function onEdit($id)
    {
        $form = AdminForm::panel()->addBody([
            AdminFormElement::slugWidgetTitle('title', 'Заголовок статьи', false)->required(),
            AdminFormElement::slugWidgetUrl('url', 'ЧПУ УРЛ статьи (генерируется автоматически из заголовка)', false)
                ->addValidationRule('unique:tests,url,' . $id . ',id')
                ->setHelpText('Дупликат ЧПУ УРЛ статьи, укажите уникальное значение'),

            AdminFormElement::select('lang_id', 'Язык', Lang::class)
                ->setDisplay('name')
                ->setHtmlAttribute('placeholder', 'Выберите язык')
                ->setDefaultValue(Lang::defaultLang())
                ->required()
            ,

            AdminFormElement::columns()
                ->addColumn([
                    AdminFormElement::image('image', 'Изображение')
                        ->setSaveCallback(function ($file, $path, $filename, $settings) use ($id) {
                            return $this->imgCustomSave($file, $path, $filename, $settings, $id);
                        })
                    ,
                ], 6)
                ->addColumn([
                    AdminFormElement::columns()
                        ->addColumn([
                            AdminFormElement::date('public_start', 'Дата публикации')
                                ->setDefaultValue(Carbon::now())
                                ->required()
                            ,
                        ], 12)
                    ,
                    AdminFormElement::textarea('overview', 'Лид'),
                ], 6)
            ,

            AdminFormElement::ckeditor('text', 'Текст')
                ->required(),

            AdminFormElement::checkbox('published', 'Опубликовано')->setDefaultValue(1),

            AdminFormElement::columns()->addColumn(['<hr>'], 12),
        ]);

        return $form;
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
