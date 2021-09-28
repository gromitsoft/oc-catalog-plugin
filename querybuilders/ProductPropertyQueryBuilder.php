<?php

namespace GromIT\Catalog\QueryBuilders;

use October\Rain\Database\Builder;

class ProductPropertyQueryBuilder extends Builder
{

    public function byProduct($product_id): ProductPropertyQueryBuilder
    {
        return $this->where('product_id', '=', $product_id);
    }

    public function byFilterGroups(array $filtered): ProductPropertyQueryBuilder
    {
        return $this->whereHas('property', function ($propertyQuery) use ($filtered) {
            $propertyQuery->whereIn('group_id', $filtered);
        });
    }
}
