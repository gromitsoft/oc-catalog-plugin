<?php

namespace GromIT\Catalog\QueryBuilders;

use October\Rain\Database\Builder;

class CategoryQueryBuilder extends Builder
{

    public function byParent($category_id): CategoryQueryBuilder
    {
        return $this->where('parent_id', '=', $category_id);
    }
}
