<?php namespace GromIT\Catalog\Models;

use GromIT\Catalog\QueryBuilders\ProductPropertyQueryBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Model;
use October\Rain\Argon\Argon;
use October\Rain\Database\Collection;
use October\Rain\Database\Relations\BelongsTo;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

/**
 * ProductProperty Model
 *
 * @property int                               $id
 * @property int                               $product_id
 * @property int                               $property_id
 * @property int                               $sort_order
 * @property Argon                             $created_at
 * @property Argon                             $updated_at
 *
 * @property Product                           $product
 * @method BelongsTo product()
 *
 * @property Property                          $property
 * @method BelongsTo property()
 *
 * @property Collection|ProductPropertyValue[] $product_property_values
 * @method HasMany product_property_values()
 *
 * @method static ProductPropertyQueryBuilder query()
 *
 */
class ProductProperty extends Model
{
    public $table = 'gromit_catalog_product_properties';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    use Validation;

    public $rules = [
        'product' => 'required',
    ];

    public $customMessages = [
        'product.required' => 'Не выбран товар',
    ];

    use Sortable;

    protected $casts = [
        'id'          => 'int',
        'product_id'  => 'int',
        'property_id' => 'int',
        'sort_order'  => 'int',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsTo = [
        'product'  => Product::class,
        'property' => Property::class,
    ];

    public $hasMany = [
        'product_property_values' => ProductPropertyValue::class,
    ];

    public function newEloquentBuilder($query): ProductPropertyQueryBuilder
    {
        return new ProductPropertyQueryBuilder($query);
    }

    public function getSavedValuesAttribute(): string
    {

        if ($this->product_property_values->count()) {
            $value_ids = $this
                ->product_property_values()
                ->pluck('value_id')
                ->all();

            $values = Value::query()
                ->whereIn('id', $value_ids)
                ->limit(10)
                ->pluck('name')
                ->all();
            return str_limit(implode(', ', $values), 50);
        }

        return '&mdash;';
    }
}
