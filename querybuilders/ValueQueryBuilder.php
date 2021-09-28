<?php

namespace GromIT\Catalog\QueryBuilders;

use October\Rain\Database\Builder;

class ValueQueryBuilder extends Builder
{

    public function byProperty($property_id): ValueQueryBuilder
    {
        return $this->where('property_id', '=', $property_id);
    }
}
