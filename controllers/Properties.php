<?php namespace GromIT\Catalog\Controllers;

use GromIT\Catalog\Models\Value;
use GromIT\Catalog\QueryBuilders\ValueQueryBuilder;
use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use BackendMenu;
use GromIT\PopupBuilder\Behaviors\PopupController;
use October\Rain\Database\Builder;

/**
 * Properties Backend Controller
 * @method listRefresh(string $string)
 */
class Properties extends Controller
{
    public $implement = [
        FormController::class,
        ListController::class,
        PopupController::class,
    ];

    public $formConfig = 'config/form/properties.yaml';

    public $listConfig = [
        'properties' => 'config/list/properties.yaml',
        'values'     => 'config/list/values.yaml',
    ];

    public $popupConfig = [
        'value' => 'config/popup/value.yaml',
    ];

    public $hiddenActions = [
        'preview',
    ];

    public function __construct()
    {
        parent::__construct();

        if (in_array($this->action, ['create', 'update'])) {
            $this->bodyClass = 'compact-container';
        }

        BackendMenu::setContext('GromIT.Catalog', 'catalog', 'properties');
    }

    public function update($recordId, $context = null)
    {
        $this->makeLists();

        return $this->asExtension('FormController')->update($recordId, $context);
    }

    public function onSaveValue()
    {
        if (!empty(post('id'))) {
            $value = Value::query()->find(post('id'));
        } else {
            $value = new Value();
        }

        if (post('is_default') && post('is_default') == 1) {
            Value::query()->byProperty($this->params[0])
                ->update(['is_default' => false]);
        }

        $value->name        = post('name');
        $value->is_default  = post('is_default');
        $value->property_id = $this->params[0];
        $value->save();

        $value->commitDeferred(post('_session_key'));

        return $this->listRefresh('values');
    }

    public function onDeleteValues()
    {
        if (!empty(post('checked'))) {
            Value::query()
                ->whereIn('id', post('checked'))
                ->delete();
        }
        return $this->listRefresh('values');
    }

    public function getPopupFormModel(string $definition, ?string $modelClass)
    {
        if ($definition === 'value') {
            return !empty(post('value_id'))
                ? Value::query()->find(post('value_id'))
                : new Value();
        }

        return $this->asExtension(PopupController::class)->getPopupFormModel($definition, $modelClass);
    }

    public function listExtendQuery(Builder $query, $definition)
    {
        if ($definition === 'values') {
            /** @var ValueQueryBuilder $query */
            $query->byProperty($this->params[0]);
        }
    }
}
