<?php namespace GromIT\Catalog\Models;

use GromIT\Catalog\QueryBuilders\CategoryQueryBuilder;
use Model;
use October\Rain\Argon\Argon;
use October\Rain\Database\Collection;
use October\Rain\Database\Relations\AttachMany;
use October\Rain\Database\Relations\AttachOne;
use October\Rain\Database\Traits\NestedTree;
use October\Rain\Database\Traits\Nullable;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;
use System\Models\File;

/**
 * Category Model
 *
 * @property int               $id
 * @property string            $name
 * @property string|null       $description
 * @property string|null       $slug
 * @property int|null          $parent_id
 * @property int|null          $nest_left
 * @property int|null          $nest_right
 * @property int|null          $nest_depth
 * @property bool              $is_active
 * @property Argon             $created_at
 * @property Argon             $updated_at
 *
 * @property File              $image
 * @method AttachOne image()
 *
 * @property Collection|File[] $images
 * @method AttachMany images()
 *
 * @method static CategoryQueryBuilder query()
 */
class Category extends Model
{
    public $table = 'gromit_catalog_categories';

    protected $guarded = [
        'id',
        'nest_left',
        'nest_right',
        'nest_depth',
        'created_at',
        'updated_at',
    ];

    use Validation;

    public $rules = [
        'name' => 'required',
    ];

    public $customMessages = [
        'name.required' => 'Название категории обязательно для заполнения'
    ];

    use NestedTree;

    use Sluggable;

    protected $slugs = [
        'slug' => ['name'],
    ];

    use Nullable;

    protected $nullable = [
        'description',
        'slug',
        'parent_id',
        'nest_left',
        'nest_right',
        'nest_depth',
    ];

    protected $casts = [
        'id'         => 'int',
        'nest_left'  => 'int',
        'nest_right' => 'int',
        'nest_depth' => 'int',
        'is_active'  => 'bool',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $hasMany = [
        'products' => Product::class,
    ];

    public $attachOne = [
        'image' => File::class,
    ];

    public $attachMany = [
        'images' => File::class,
    ];

    public function newEloquentBuilder($query): CategoryQueryBuilder
    {
        return new CategoryQueryBuilder($query);
    }
}
