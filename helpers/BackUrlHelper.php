<?php

namespace GromIT\Catalog\Helpers;

use Backend\Facades\Backend;

class BackUrlHelper
{

    public static function hasUrl(): bool
    {
        if (get('controller') && get('action') && get('record_id')) {
            return true;
        }
        return false;
    }

    public static function getUrl(): string
    {
        return Backend::url(
            'gromit/catalog/'
            . get('controller') . '/'
            . get('action') . '/'
            . get('record_id')
        );
    }

}
