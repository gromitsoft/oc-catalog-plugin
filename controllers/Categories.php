<?php namespace GromIT\Catalog\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use BackendMenu;
use GromIT\Catalog\Models\Category;
use GromIT\Catalog\QueryBuilders\CategoryQueryBuilder;
use GromIT\PopupBuilder\Behaviors\PopupController;
use October\Rain\Database\Builder;

/**
 * Categories Backend Controller
 * @method listRefresh(string $string)
 * @method makeLists()
 */

class Categories extends Controller
{
    public $implement = [
        FormController::class,
        ListController::class,
        PopupController::class,
    ];

    public $formConfig = 'config/form/categories.yaml';

    public $listConfig = [
        'categories' => 'config/list/categories.yaml',
        'children'   => 'config/list/children.yaml',
    ];

    public $popupConfig = [
        'child' => 'config/popup/child.yaml',
    ];

    public $hiddenActions = [
        'preview',
    ];

    public function __construct()
    {
        parent::__construct();

        if(in_array($this->action, ['create', 'update'])){
            $this->bodyClass = 'compact-container';
        }

        BackendMenu::setContext('GromIT.Catalog', 'catalog', 'categories');
    }

    public function update($recordId, $context = null)
    {

        $this->makeLists();

        return $this->asExtension('FormController')->update($recordId, $context);
    }

    public function onSaveChild()
    {
        if (!empty(post('id'))) {
            $category = Category::query()->find(post('id'));
        } else {
            $category = new Category();
        }
        $category->name      = post('name');
        $category->parent_id = $this->params[0];

        $category->save();

        return $this->listRefresh('children');
    }

    public function onDeleteChildren()
    {
        if (!empty(post('checked'))) {
            Category::query()
                ->whereIn('id', post('checked'))
                ->delete();
        }
        return $this->listRefresh('children');
    }

    public function onChangeActive(): array
    {
        $category = Category::query()
            ->find(post('category_id'));

        /** @var Category $category */
        if ($category->is_active) {
            $category->is_active = false;
        } else {
            $category->is_active = true;
        }
        $category->save();

        return $this->listRefresh(post('list'));
    }

    public function getPopupFormModel(string $definition, ?string $modelClass)
    {
        if ($definition === 'child') {
            return !empty(post('child_id'))
                ? Category::query()->find(post('child_id'))
                : new Category();
        }

        return $this->asExtension(PopupController::class)->getPopupFormModel($definition, $modelClass);
    }

    public function listExtendQuery(Builder $query, $definition)
    {
        if ($definition === 'children') {
            /** @var CategoryQueryBuilder $query */
            $query->byParent($this->params[0]);
        }
    }

}
