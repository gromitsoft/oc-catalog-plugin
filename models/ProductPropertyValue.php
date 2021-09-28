<?php namespace GromIT\Catalog\Models;

use GromIT\Catalog\QueryBuilders\ProductPropertyValueQueryBuilder;
use Model;
use October\Rain\Database\Relations\BelongsTo;
use October\Rain\Database\Traits\Validation;

/**
 * ProductPropertyValue Model
 *
 * @property int             $id
 * @property int             $product_property_id
 * @property int             $value_id
 *
 * @property ProductProperty $product_property
 * @method BelongsTo product_property()
 *
 * @property Value           $value
 * @method BelongsTo value()
 *
 * @method static ProductPropertyValueQueryBuilder query()
 *
 */
class ProductPropertyValue extends Model
{
    public $table = 'gromit_catalog_product_property_values';

    public $timestamps = false;

    use Validation;

    public $rules = [
        'product_property_id' => 'required',
        'value_id'            => 'required',
    ];

    public $customMessages = [
        'product_property_id.required' => 'Не выбрана характеристика товара',
        'value_id.required'            => 'Не выбрано значение',
    ];

    protected $casts = [
        'id'                  => 'int',
        'product_property_id' => 'int',
        'value_id'            => 'int',
    ];


    public $belongsTo = [
        'product_property' => ProductProperty::class,
        'value'            => Value::class,
    ];

    public function newEloquentBuilder($query): ProductPropertyValueQueryBuilder
    {
        return new ProductPropertyValueQueryBuilder($query);
    }

}
