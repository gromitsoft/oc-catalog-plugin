<?php namespace GromIT\Catalog\Models;

use GromIT\Catalog\QueryBuilders\GroupQueryBuilder;
use Model;
use October\Rain\Argon\Argon;
use October\Rain\Database\Collection;
use October\Rain\Database\Relations\AttachMany;
use October\Rain\Database\Relations\AttachOne;
use October\Rain\Database\Relations\HasMany;
use October\Rain\Database\Traits\Nullable;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;
use System\Models\File;

/**
 * Group Model
 *
 * @property int                   $id
 * @property string                $name
 * @property string|null           $alt_name
 * @property string|null           $description
 * @property string|null           $slug
 * @property int                   $sort_order
 * @property Argon                 $created_at
 * @property Argon                 $updated_at
 *
 * @property Collection|Property[] $properties
 * @method HasMany properties()
 *
 * @property File                  $image
 * @method AttachOne image()
 *
 * @property Collection|File[]     $images
 * @method AttachMany images()
 *
 * @method static GroupQueryBuilder query()
 */
class Group extends Model
{
    public $table = 'gromit_catalog_groups';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    use Validation;

    public $rules = [
        'name' => 'required|unique:gromit_catalog_groups'
    ];

    public $customMessages = [
        'name.required' => 'Необходимо указать название набора характеристик',
        'name.unique'   => 'Такое название набора характеристик уже есть в справочнике',
    ];

    use Nullable;

    protected $nullable = [
        'alt_name',
        'description',
        'slug',
    ];

    use Sluggable;

    protected $slugs = [
        'slug' => ['name'],
    ];

    use Sortable;

    protected $casts = [
        'id'         => 'int',
        'sort_order' => 'int',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $hasMany = [
        'properties' => Property::class,
    ];

    public $attachOne = [
        'image' => File::class,
    ];

    public $attachMany = [
        'images' => File::class,
    ];

    public function newEloquentBuilder($query): GroupQueryBuilder
    {
        return new GroupQueryBuilder($query);
    }

    public function getGroupIdOptions(): array
    {
        return self::query()
            ->pluck('name', 'id')
            ->all();
    }
}
