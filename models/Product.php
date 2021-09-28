<?php namespace GromIT\Catalog\Models;

use GromIT\Catalog\QueryBuilders\ProductQueryBuilder;
use Model;
use October\Rain\Argon\Argon;
use October\Rain\Database\Collection;
use October\Rain\Database\Relations\AttachMany;
use October\Rain\Database\Relations\AttachOne;
use October\Rain\Database\Relations\BelongsTo;
use October\Rain\Database\Relations\HasMany;
use October\Rain\Database\Traits\Nullable;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\Validation;
use System\Models\File;

/**
 * Product Model
 *
 * @property int                        $id
 * @property int                        $category_id
 * @property string                     $name
 * @property string|null                $description
 * @property string|null                $vendor_code
 * @property string|null                $slug
 * @property bool                       $is_active
 * @property Argon                      $created_at
 * @property Argon                      $updated_at
 *
 * @property Category                   $category
 * @method BelongsTo category()
 *
 * @property Collection|Offer[]         $offers
 * @method HasMany offers()
 *
 * @property Collection|ProductProperty $product_properties
 * @method HasMany product_properties()
 *
 * @property File                       $image
 * @method AttachOne image()
 *
 * @property Collection|File[]          $images
 * @method AttachMany images()
 *
 * @method static ProductQueryBuilder query()
 *
 */
class Product extends Model
{
    public $table = 'gromit_catalog_products';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    use Validation;

    public $rules = [
        'name'     => 'required',
        'category' => 'required',
    ];

    public $customMessages = [
        'name.required'     => 'Не заполнено название товара',
        'category.required' => 'Не выбрана категория товара',
    ];

    use Nullable;

    protected $nullable = [
        'vendor_code',
        'description',
    ];

    use Sluggable;

    protected $slugs = [
        'slug' => ['name'],
    ];

    protected $casts = [
        'id'          => 'int',
        'category_id' => 'int',
        'is_active'   => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $hasMany = [
        'offers'             => Offer::class,
        'product_properties' => ProductProperty::class,
    ];

    public $belongsTo = [
        'category' => Category::class,
    ];

    public $attachOne = [
        'image' => File::class,
    ];

    public $attachMany = [
        'images' => File::class,
    ];

    public function newEloquentBuilder($query): ProductQueryBuilder
    {
        return new ProductQueryBuilder($query);
    }

    public function getFullCategoryAttribute(): string
    {
        $categories = $this
            ->category
            ->getParentsAndSelf()
            ->pluck('name', 'id')
            ->all();
        return implode(' / ', $categories);
    }
}
