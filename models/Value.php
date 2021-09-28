<?php namespace GromIT\Catalog\Models;

use GromIT\Catalog\QueryBuilders\ValueQueryBuilder;
use Model;
use October\Rain\Argon\Argon;
use October\Rain\Database\Relations\AttachOne;
use October\Rain\Database\Relations\BelongsTo;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;
use System\Models\File;

/**
 * Value Model
 * @property int      $id
 * @property int      $property_id
 * @property string   $name
 * @property bool     $is_default
 * @property int      $sort_order
 * @property Argon    $created_at
 * @property Argon    $updated_at
 *
 * @property Property $property
 * @method BelongsTo property()
 *
 * @property File                       $image
 * @method AttachOne image()
 *
 * @method static ValueQueryBuilder query()
 */
class Value extends Model
{
    public $table = 'gromit_catalog_values';

    protected $guarded = ['id'];

    use Validation;

    public $rules = [
        'name'     => 'required',
        'property' => 'required',
    ];

    public $customMessages = [
        'name.required'     => 'Не указано значение характеристики',
        'property.required' => 'Не указана характеристика',
    ];

    use Sortable;

    protected $casts = [
        'id'          => 'int',
        'property_id' => 'int',
        'is_default'  => 'bool',
        'sort_order'  => 'int',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsTo = [
        'property' => Property::class,
    ];

    public $attachOne = [
        'image' => File::class,
    ];

    public function newEloquentBuilder($query): ValueQueryBuilder
    {
        return new ValueQueryBuilder($query);
    }
}
