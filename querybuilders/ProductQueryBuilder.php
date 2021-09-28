<?php

namespace GromIT\Catalog\QueryBuilders;

use October\Rain\Database\Builder;

class ProductQueryBuilder extends Builder
{
    public function onlyActive(): ProductQueryBuilder
    {
        return $this->where('is_active', true);
    }
}
