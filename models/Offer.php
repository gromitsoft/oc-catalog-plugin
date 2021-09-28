<?php namespace GromIT\Catalog\Models;

use GromIT\Catalog\QueryBuilders\OfferQueryBuilder;
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
 * Offer Model
 *
 * @property int                        $id
 * @property int|null                   $product_id
 * @property string                     $name
 * @property string|null                $slug
 * @property string|null                $vendor_code
 * @property string|null                $description
 * @property int                        $sort_order
 * @property bool                       $is_active
 * @property Argon                      $created_at
 * @property Argon                      $updated_at
 *
 * @property Product                    $product
 * @method BelongsTo product()
 *
 * @property File                       $image
 * @method AttachOne image()
 *
 * @property Collection|File[]          $images
 * @method AttachMany images()
 *
 * @property Collection|OfferProperty[] $offer_properties
 * @method HasMany offer_properties()
 *
 * @method static OfferQueryBuilder query()
 */
class Offer extends Model
{
    public $table = 'gromit_catalog_offers';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    use Validation;

    public $rules = [
        'name'        => 'required',
        'vendor_code' => 'nullable|unique:gromit_catalog_offers'
    ];

    public $customMessages = [
        'name.required'      => 'Не заполнено название товара',
        'vendor_code.unique' => 'Запись с таким артикулом уже есть',
    ];

    use Nullable;

    protected $nullable = [
        'product_id',
        'slug',
        'vendor_code',
        'description',
    ];

    use Sluggable;

    protected $slugs = [
        'slug' => ['name'],
    ];

    use Sortable;

    protected $casts = [
        'id'         => 'int',
        'product_id' => 'int',
        'sort_order' => 'int',
        'is_active'  => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    public $belongsTo = [
        'product' => Product::class,
    ];

    public $hasMany = [
        'offer_properties' => OfferProperty::class,
    ];

    public $attachOne = [
        'image' => File::class,
    ];

    public $attachMany = [
        'images' => File::class,
    ];

    public function newEloquentBuilder($query): OfferQueryBuilder
    {
        return new OfferQueryBuilder($query);
    }

    public function getFullCategoryAttribute(): string
    {
        if ($this->product) {
            $categories = $this
                ->product
                ->category
                ->getParentsAndSelf()
                ->pluck('name', 'id')
                ->all();
            return implode(' / ', $categories);
        }
        return 'Не привязан к категории';
    }
}
