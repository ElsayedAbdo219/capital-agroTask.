<?php

namespace Modules\Product\Enums;

enum ProductStatus
{
    const ACRIVE = 'active';

    const INACRIVE = 'inactive';

    public static function toArray()
    {
        return [
            self::ACRIVE => 'active',
            self::INACRIVE => 'inactive',
        ];
    }
}
