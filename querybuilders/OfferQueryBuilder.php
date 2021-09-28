<?php

namespace GromIT\Catalog\QueryBuilders;

use October\Rain\Database\Builder;

class OfferQueryBuilder extends Builder
{
    public function onlyActive(): OfferQueryBuilder
    {
        return $this->where('is_active', true);
    }

    public function withoutProduct(): OfferQueryBuilder
    {
        return $this->whereNull('product_id');
    }

    public function byProduct($product_id): OfferQueryBuilder
    {
        return $this->where('product_id', '=', $product_id);
    }
}
