<?php namespace GromIT\Catalog\Models;

use GromIT\Catalog\QueryBuilders\OfferPropertyValueQueryBuilder;
use Model;
use October\Rain\Database\Relations\BelongsTo;
use October\Rain\Database\Traits\Validation;

/**
 * OfferPropertyValue Model
 *
 * @property int           $id
 * @property int           $offer_property_id
 * @property int           $value_id
 *
 * @property OfferProperty $offer_property
 * @method BelongsTo offer_property()
 *
 * @property Value         $value
 * @method BelongsTo value()
 *
 * @method static OfferPropertyValueQueryBuilder query()
 *
 */
class OfferPropertyValue extends Model
{
    public $table = 'gromit_catalog_offer_property_values';

    public $timestamps = false;

    use Validation;

    public $rules = [
        'offer_property_id' => 'required',
        'value_id'          => 'required',
    ];

    public $customMessages = [
        'offer_property_id.required' => 'Не выбрана характеристика предложения',
        'value_id.required'          => 'Не выбрано значение',
    ];

    protected $casts = [
        'id'                => 'int',
        'offer_property_id' => 'int',
        'value_id'          => 'int',
    ];


    public $belongsTo = [
        'offer_property' => OfferProperty::class,
        'value'          => Value::class,
    ];

    public function newEloquentBuilder($query): OfferPropertyValueQueryBuilder
    {
        return new OfferPropertyValueQueryBuilder($query);
    }
}
