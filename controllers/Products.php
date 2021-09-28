<?php namespace GromIT\Catalog\Controllers;

use Backend\Behaviors\FormController;
use Backend\Behaviors\ListController;
use Backend\Classes\Controller;
use BackendMenu;
use GromIT\Catalog\Models\Group;
use GromIT\Catalog\Models\Offer;
use GromIT\Catalog\Models\Product;
use GromIT\Catalog\Models\ProductProperty;
use GromIT\Catalog\Models\ProductPropertyValue;
use GromIT\Catalog\Models\Property;
use GromIT\Catalog\Models\Value;
use GromIT\Catalog\QueryBuilders\OfferQueryBuilder;
use GromIT\Catalog\QueryBuilders\ProductPropertyQueryBuilder;
use GromIT\PopupBuilder\Behaviors\PopupController;
use October\Rain\Database\Builder;
use October\Rain\Exception\ValidationException;

/**
 * Products Backend Controller
 */
class Products extends Controller
{
    public $implement = [
        FormController::class,
        ListController::class,
        PopupController::class,
    ];

    public $formConfig = 'config/form/products.yaml';

    public $listConfig = [
        'products'           => 'config/list/products.yaml',
        'product_properties' => 'config/list/product_properties.yaml',
        'product_offers'     => 'config/list/product_offers.yaml',
        'offers_select'      => 'config/list/offers_select.yaml'
    ];

    public $popupConfig = [
        'group'                  => 'config/popup/group.yaml',
        'property'               => 'config/popup/property.yaml',
        'product_property_value' => 'config/popup/product_property_value.yaml',
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

        BackendMenu::setContext('GromIT.Catalog', 'catalog', 'products');
    }

    public function update($recordId, $context = null)
    {
        $this->makeLists();

        return $this->asExtension('FormController')->update($recordId, $context);
    }

    public function onChangeActive(): array
    {
        if (post('list') === 'products') {
            /** @var Product $model */
            $model = Product::query()
                ->find(post('id'));
        }

        if (post('list') === 'product_offers') {
            /** @var Offer $model */
            $model = Offer::query()
                ->find(post('id'));
        }

        if ($model->is_active) {
            $model->is_active = false;
        } else {
            $model->is_active = true;
        }
        $model->save();

        return $this->listRefresh(post('list'));
    }

    public function onAddGroup()
    {
        if (!empty(post('group_id'))) {

            /** @var Group $group */
            $group = Group::query()
                ->find(post('group_id'));

            foreach ($group->properties as $property) {
                $exist = ProductProperty::query()
                    ->where('product_id', '=', $this->params[0])
                    ->where('property_id', '=', $property->id)
                    ->first();
                if (!$exist) {
                    $product_property              = new ProductProperty();
                    $product_property->product_id  = $this->params[0];
                    $product_property->property_id = $property->id;
                    $product_property->save();
                }
            }
        }

        return $this->listRefresh('product_properties');
    }

    public function onOpenValueForm()
    {
        $product_property = ProductProperty::query()
            ->find(post('product_property_id'));

        if ($product_property) {

            /** @var ProductProperty $product_property */

            $property = Property::query()->find($product_property->property_id);

            return $this->makePartial('partials/product_property_values_form',
                [
                    'property'         => $property,
                    'product_property' => $product_property
                ]);
        }
    }

    public function onDeleteProductProperties()
    {
        if (!empty(post('checked'))) {
            ProductProperty::query()
                ->whereIn('id', post('checked'))
                ->delete();
        }
        return $this->listRefresh('product_properties');
    }

    public function onOpenProductPropertyForm()
    {
        $product = Product::query()
            ->find($this->params[0]);

        /** @var Product $product */

        $used_properties = $product
            ->product_properties()
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

        return $this->makePartial('partials/product_property_property_form',
            [
                'properties_with_group'    => $properties_with_group,
                'properties_without_group' => $properties_without_group,
            ]);
    }

    public function onSaveProductProperty()
    {
        $property = Property::query()
            ->find(post('property_id'));

        if ($property) {
            $exist = ProductProperty::query()
                ->where('product_id', '=', $this->params[0])
                ->where('property_id', '=', $property->id)
                ->first();

            if (!$exist) {
                $product_property              = new ProductProperty();
                $product_property->product_id  = $this->params[0];
                $product_property->property_id = $property->id;
                $product_property->save();
            }
        }
        return $this->listRefresh('product_properties');
    }

    public function onOpenNewValueForm()
    {
        $product_property = ProductProperty::query()
            ->find(post('product_property_id'));

        /** @var ProductProperty $product_property */

        return $this->makePartial('partials/product_property_values_new_value_form',
            [
                'product_property' => $product_property,
            ]);
    }

    /**
     * @throws ValidationException
     */
    public function onSaveProductPropertyValues()
    {
        $product_property_id = post('product_property_id');
        $values              = post('values');

        if (empty($values))
            throw new ValidationException(['product_property_id' => 'Не выбрано значение']);

        if (!is_array($values)) {
            $values = [$values];
        }

        ProductPropertyValue::query()
            ->where('product_property_id', '=', $product_property_id)
            ->delete();

        $insertData = [];

        foreach ($values as $value) {
            $insertData[] = [
                'product_property_id' => $product_property_id,
                'value_id'            => $value,
            ];
        }

        if (!empty($insertData)) {
            ProductPropertyValue::query()
                ->insert($insertData);
        }

        return $this->listRefresh('product_properties');
    }

    /**
     * @throws \SystemException
     */
    public function onSaveProductPropertyNewValue(): array
    {
        /** @var ProductProperty $product_property */
        $product_property = ProductProperty::query()
            ->find(post('product_property_id'));

        /** @var Property $property */
        $property = Property::query()
            ->find($product_property->property_id);

        $new_value = post('new_value');

        $value              = new Value();
        $value->property_id = $property->id;
        $value->name        = $new_value;
        $value->save();

        return [
            '#values_list' => $this->makePartial(
                'partials/product_property_values_list',
                [
                    'property'         => $property,
                    'product_property' => $product_property,
                ]
            ),
        ];
    }

    /**
     * @throws \SystemException
     */
    public function onOpenProductOfferForm()
    {
        return $this->makePartial('partials/product_offer_form');
    }

    public function onSaveProductOffer()
    {
        $offer              = new Offer();
        $offer->name        = post('name');
        $offer->vendor_code = post('vendor_code');
        $offer->product_id  = $this->params[0];
        $offer->save();
        return $this->listRefresh('product_offers');
    }

    public function onShowFreeOffers()
    {
        $this->makeLists();

        return $this->makePartial('partials/select_offers', ['controller' => $this]);
    }

    public function onLinkOffers()
    {
        if (!empty(post('checked'))) {
            Offer::query()
                ->whereIn('id', post('checked'))
                ->update(['product_id' => $this->params[0]]);
        }

        return $this->listRefresh('product_offers');
    }

    public function listExtendQuery(Builder $query, $definition)
    {
        if ($definition === 'product_properties') {
            /** @var ProductPropertyQueryBuilder $query */
            $query->byProduct($this->params[0]);
        }

        if ($definition === 'product_offers') {
            /** @var OfferQueryBuilder $query */
            $query->byProduct($this->params[0]);
        }

        if ($definition === 'offers_select') {
            /** @var OfferQueryBuilder $query */
            $query->withoutProduct();
        }
    }

    private function addFilterOptions()
    {
        ProductProperty::extend(function (ProductProperty $model) {

            $model->addDynamicMethod('getFilterGroupsOptions', function () {

                $property_ids = ProductProperty::query()
                    ->where('product_id', '=', $this->params[0])
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
