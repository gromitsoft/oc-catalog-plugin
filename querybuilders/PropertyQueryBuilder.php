<?php

namespace GromIT\Catalog\QueryBuilders;

use October\Rain\Database\Builder;

class PropertyQueryBuilder extends Builder
{

    public function byGroup($group_id): PropertyQueryBuilder
    {
        return $this->where('group_id', '=', $group_id);
    }

    public function withoutGroup(): PropertyQueryBuilder
    {
        return $this->whereNull('group_id');
    }

    public function withGroup(): PropertyQueryBuilder
    {
        return $this->whereNotNull('group_id');
    }
}
