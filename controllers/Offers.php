<?php namespace GromIT\Catalog\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use BackendMenu;
use GromIT\Catalog\Models\Group;
use GromIT\Catalog\Models\Offer;
use GromIT\Catalog\Models\OfferProperty;
use GromIT\Catalog\Models\OfferPropertyValue;
use GromIT\Catalog\Models\Property;
use GromIT\Catalog\Models\Value;
use GromIT\Catalog\QueryBuilders\OfferPropertyQueryBuilder;
use GromIT\PopupBuilder\Behaviors\PopupController;
use October\Rain\Database\Builder;
use October\Rain\Exception\ValidationException;

/**
 * Offers Backend Controller
 */
class Offers extends Controller
{
    public $implement = [
        FormController::class,
        ListController::class,
        PopupController::class,
    ];

    public $formConfig = 'config/form/offers.yaml';

    public $listConfig = [
        'offers'           => 'config/list/offers.yaml',
        'offer_properties' => 'config/list/offer_properties.yaml',
    ];

    public $popupConfig = [
        'group'                  => 'config/popup/group.yaml',
        'property'               => 'config/popup/property.yaml',
        'product_property_value' => 'config/popup/offer_property_value.yaml',
    ];

    public $hiddenActions = [
        'preview',
    ];

    public function __construct()
    {
        parent::__construct();

        if ($this->action === 'update') {
            $this->addFilterOptions();
        }

        if (in_array($this->action, ['create', 'update'])) {
            $this->bodyClass = 'compact-container';
        }

        BackendMenu::setContext('GromIT.Catalog', 'catalog', 'offers');
    }

    public function update($recordId, $context = null)
    {
        $this->makeLists();

        return $this->asExtension('FormController')->update($recordId, $context);
    }

    public function onAddGroup()
    {
        if (!empty(post('group_id'))) {

            /** @var Group $group */
            $group = Group::query()
                ->find(post('group_id'));

            foreach ($group->properties as $property) {
                $exist = OfferProperty::query()
                    ->where('offer_id', '=', $this->params[0])
                    ->where('property_id', '=', $property->id)
                    ->first();
                if (!$exist) {
                    $offer_property              = new OfferProperty();
                    $offer_property->offer_id    = $this->params[0];
                    $offer_property->property_id = $property->id;
                    $offer_property->save();
                }
            }
        }

        return $this->listRefresh('offer_properties');
    }

    public function onOpenValueForm()
    {
        $offer_property = OfferProperty::query()
            ->find(post('offer_property_id'));

        if ($offer_property) {

            /** @var OfferProperty $offer_property */

            $property = Property::query()->find($offer_property->property_id);

            return $this->makePartial('partials/offer_property_values_form',
                [
                    'property'       => $property,
                    'offer_property' => $offer_property
                ]);
        }
    }

    public function onDeleteOfferProperties()
    {
        if (!empty(post('checked'))) {
            OfferProperty::query()
                ->whereIn('id', post('checked'))
                ->delete();
        }
        return $this->listRefresh('offer_properties');
    }

    public function onOpenOfferPropertyForm()
    {
        $offer = Offer::query()
            ->find($this->params[0]);

        /** @var Offer $offer */

        $used_properties = $offer
            ->offer_properties()
            ->pluck('property_id')
            ->all();

        $groups = Group::query()
            ->whereHas('properties')
            ->get();

        $properties_with_group = [];

        foreach ($groups as $group) {
            /** @var Group $group */
            $properties_with_group[] = [
                'group_id'   => $group->id,
                'group_name' => $group->name,
                'properties' => $group
                    ->properties()
                    ->whereNotIn('id', $used_properties)
                    ->pluck('name', 'id')
                    ->all(),
            ];
        }

        $properties_without_group = Property::query()
            ->withoutGroup()
            ->whereNotIn('id', $used_properties)
            ->pluck('name', 'id')
            ->all();

        return $this->makePartial('partials/offer_property_property_form',
            [
                'properties_with_group'    => $properties_with_group,
                'properties_without_group' => $properties_without_group,
            ]);
    }

    public function onSaveOfferProperty()
    {
        $property = Property::query()
            ->find(post('property_id'));

        if ($property) {
            $exist = OfferProperty::query()
                ->where('offer_id', '=', $this->params[0])
                ->where('property_id', '=', $property->id)
                ->first();

            if (!$exist) {
                $offer_property              = new OfferProperty();
                $offer_property->offer_id    = $this->params[0];
                $offer_property->property_id = $property->id;
                $offer_property->save();
            }
        }
        return $this->listRefresh('offer_properties');
    }

    public function onOpenNewValueForm()
    {
        $offer_property = OfferProperty::query()
            ->find(post('offer_property_id'));

        /** @var OfferProperty $offer_property */

        return $this->makePartial('partials/offer_property_values_new_value_form',
            [
                'offer_property' => $offer_property,
            ]);
    }

    public function onChangeActive(): array
    {
        /** @var Offer $model */
        $model = Offer::query()
            ->find(post('id'));

        if ($model->is_active) {
            $model->is_active = false;
        } else {
            $model->is_active = true;
        }
        $model->save();

        return $this->listRefresh(post('list'));
    }

    /**
     * @throws ValidationException
     */
    public function onSaveOfferPropertyValues()
    {
        $offer_property_id = post('offer_property_id');
        $values            = post('values');

        if (empty($values))
            throw new ValidationException(['offer_property_id' => 'Не выбрано значение']);

        if (!is_array($values)) {
            $values = [$values];
        }

        OfferPropertyValue::query()
            ->where('offer_property_id', '=', $offer_property_id)
            ->delete();

        $insertData = [];

        foreach ($values as $value) {
            $insertData[] = [
                'offer_property_id' => $offer_property_id,
                'value_id'          => $value,
            ];
        }

        if (!empty($insertData)) {
            OfferPropertyValue::query()
                ->insert($insertData);
        }

        return $this->listRefresh('offer_properties');
    }

    /**
     * @throws \SystemException
     */
    public function onSaveOfferPropertyNewValue(): array
    {
        /** @var OfferProperty $offer_property */
        $offer_property = OfferProperty::query()
            ->find(post('offer_property_id'));

        /** @var Property $property */
        $property = Property::query()
            ->find($offer_property->property_id);

        $new_value = post('new_value');

        $value              = new Value();
        $value->property_id = $property->id;
        $value->name        = $new_value;
        $value->save();

        return [
            '#values_list' => $this->makePartial(
                'partials/offer_property_values_list',
                [
                    'property'       => $property,
                    'offer_property' => $offer_property,
                ]
            ),
        ];
    }

    public function listExtendQuery(Builder $query, $definition)
    {
        if ($definition === 'offer_properties') {
            /** @var OfferPropertyQueryBuilder $query */
            $query->byOffer($this->params[0]);
        }
    }

    private function addFilterOptions()
    {
        OfferProperty::extend(function (OfferProperty $model) {

            $model->addDynamicMethod('getFilterGroupsOptions', function () {

                $property_ids = OfferProperty::query()
                    ->where('offer_id', '=', $this->params[0])
                    ->pluck('property_id')
                    ->all();

                $group_ids = Property::query()
                    ->whereIn('id', $property_ids)
                    ->pluck('group_id')
                    ->all();

                return Group::query()
                    ->whereIn('id', $group_ids)
                    ->pluck('name', 'id')
                    ->all();
            });
        });
    }
}
