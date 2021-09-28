<?php namespace GromIT\Catalog\Models;

use GromIT\Catalog\QueryBuilders\OfferPropertyQueryBuilder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Model;
use October\Rain\Argon\Argon;
use October\Rain\Database\Collection;
use October\Rain\Database\Relations\BelongsTo;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

/**
 * OfferProperty Model
 *
 * @property int                             $id
 * @property int                             $offer_id
 * @property int                             $property_id
 * @property int                             $sort_order
 * @property Argon                           $created_at
 * @property Argon                           $updated_at
 *
 * @property Offer                           $offer
 * @method BelongsTo offer()
 *
 * @property Property                        $property
 * @method BelongsTo property()
 *
 * @property Collection|OfferPropertyValue[] $offer_property_values
 * @method HasMany offer_property_values()
 *
 * @method static OfferPropertyQueryBuilder query()
 *
 */
class OfferProperty extends Model
{
    public $table = 'gromit_catalog_offer_properties';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    use Validation;

    public $rules = [
        'offer' => 'required',
    ];

    public $customMessages = [
        'offer.required' => 'Не выбрано предложение',
    ];

    use Sortable;

    protected $casts = [
        'id'          => 'int',
        'offer_id'    => 'int',
        'property_id' => 'int',
        'sort_order'  => 'int',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsTo = [
        'offer'    => Offer::class,
        'property' => Property::class,
    ];

    public $hasMany = [
        'offer_property_values' => OfferPropertyValue::class,
    ];

    public function newEloquentBuilder($query): OfferPropertyQueryBuilder
    {
        return new OfferPropertyQueryBuilder($query);
    }

    public function getSavedValuesAttribute(): string
    {

        if ($this->offer_property_values->count()) {
            $value_ids = $this
                ->offer_property_values()
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
