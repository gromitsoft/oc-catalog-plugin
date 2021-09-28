<?php

namespace GromIT\Catalog\QueryBuilders;

use October\Rain\Database\Builder;

class OfferPropertyQueryBuilder extends Builder
{

    public function byOffer($offer_id): OfferPropertyQueryBuilder
    {
        return $this->where('offer_id', '=', $offer_id);
    }
}
