<?php namespace GromIT\Catalog\Controllers;

use GromIT\Catalog\Models\Property;
use GromIT\Catalog\QueryBuilders\PropertyQueryBuilder;
use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use BackendMenu;
use GromIT\PopupBuilder\Behaviors\PopupController;
use October\Rain\Database\Builder;

/**
 * Groups Backend Controller
 */
class Groups extends Controller
{
    public $implement = [
        FormController::class,
        ListController::class,
        PopupController::class,
    ];

    public $formConfig = 'config/form/groups.yaml';

    public $listConfig = [
        'groups'            => 'config/list/groups.yaml',
        'properties'        => 'config/list/properties.yaml',
        'properties_select' => 'config/list/properties_select.yaml'
    ];

    public $popupConfig = [
        'property' => 'config/popup/property.yaml',
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

        BackendMenu::setContext('GromIT.Catalog', 'catalog', 'groups');
    }

    public function update($recordId, $context = null)
    {
        $this->makeLists();

        return $this->asExtension('FormController')->update($recordId, $context);
    }

    public function onSaveProperty()
    {
        if (!empty(post('id'))) {
            $property = Property::query()->find(post('id'));
        } else {
            $property = new Property();
        }
        $property->name     = post('name');
        $property->alt_name = post('alt_name');
        $property->group_id = $this->params[0];
        $property->multiple = post('multiple');

        $property->save();

        return $this->listRefresh('properties');
    }

    public function onShowFreeProperties()
    {
        $this->makeLists();

        return $this->makePartial('partials/select_properties', ['controller' => $this]);
    }

    public function onLinkProperties()
    {
        if (!empty(post('checked'))) {
            Property::query()
                ->whereIn('id', post('checked'))
                ->update(['group_id' => $this->params[0]]);
        }
        return $this->listRefresh('properties');
    }

    public function onUnlinkProperties()
    {
        if (!empty(post('checked'))) {
            Property::query()
                ->whereIn('id', post('checked'))
                ->update(['group_id' => null]);
        }
        return $this->listRefresh('properties');
    }

    public function onDeleteProperties()
    {
        if (!empty(post('checked'))) {
            Property::query()
                ->whereIn('id', post('checked'))
                ->delete();
        }
        return $this->listRefresh('properties');
    }

    public function getPopupFormModel(string $definition, ?string $modelClass)
    {
        if ($definition === 'property') {
            return !empty(post('property_id'))
                ? Property::query()->find(post('property_id'))
                : new Property();
        }

        return $this->asExtension(PopupController::class)->getPopupFormModel($definition, $modelClass);
    }

    public function listExtendQuery(Builder $query, $definition)
    {
        if ($definition === 'properties') {
            /** @var PropertyQueryBuilder $query */
            $query->byGroup($this->params[0]);
        }

        if ($definition === 'properties_select') {
            /** @var PropertyQueryBuilder $query */
            $query->withoutGroup();
        }
    }
}
