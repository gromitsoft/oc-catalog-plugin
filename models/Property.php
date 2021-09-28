<?php namespace GromIT\Catalog\Models;

use GromIT\Catalog\QueryBuilders\PropertyQueryBuilder;
use Model;
use October\Rain\Argon\Argon;
use October\Rain\Database\Collection;
use October\Rain\Database\Relations\AttachMany;
use October\Rain\Database\Relations\AttachOne;
use October\Rain\Database\Relations\BelongsTo;
use October\Rain\Database\Relations\HasMany;
use October\Rain\Database\Traits\Nullable;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;
use System\Models\File;

/**
 * Property Model
 * @property int               $id
 * @property int|null          $group_id
 * @property string            $name
 * @property string|null       $alt_name
 * @property bool              $multiple
 * @property int               $sort_order
 * @property string|null       $slug
 * @property Argon             $created_at
 * @property Argon             $updated_at
 *
 * @property Collection|Value  $values
 * @method HasMany values()
 *
 * @property Group             $group
 * @method BelongsTo group()
 *
 * @property File              $image
 * @method AttachOne image()
 *
 * @property Collection|File[] $images
 * @method AttachMany images()
 *
 * @method static PropertyQueryBuilder query()
 *
 */
class Property extends Model
{
    public $table = 'gromit_catalog_properties';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    use Validation;

    public $rules = [
        'name' => 'required|unique:gromit_catalog_properties',
    ];

    public $customMessages = [
        'name.required' => 'Название характеристики обязательно для заполнения',
        'name.unique'   => 'Характеристика с таким названием уже есть в справочнике',
    ];

    use Sluggable;

    protected $slugs = [
        'slug' => ['name'],
    ];

    use Nullable;

    protected $nullable = [
        'group_id',
        'alt_name',
    ];

    use Sortable;

    protected $casts = [
        'id'         => 'int',
        'group_id'   => 'int',
        'multiple'   => 'bool',
        'sort_order' => 'int',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsTo = [
        'group' => Group::class,
    ];

    public $hasMany = [
        'values' => Value::class,
    ];

    public $attachOne = [
        'image' => File::class,
    ];

    public $attachMany = [
        'images' => File::class,
    ];

    public function newEloquentBuilder($query): PropertyQueryBuilder
    {
        return new PropertyQueryBuilder($query);
    }

    public function getValuesListAttribute(): string
    {
        $values = $this->values()->limit(10)->pluck('name')->all();
        return str_limit(implode(', ', $values), 50);
    }

    public function getPropertyIdOptions()
    {
        return self::query()
            ->pluck('name', 'id')
            ->all();
    }

}
