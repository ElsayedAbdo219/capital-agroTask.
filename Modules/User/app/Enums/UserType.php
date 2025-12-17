<?php

namespace Modules\User\Enums;

enum UserType
{
    const ADMIN = 'admin';

    const SUPPLIER = 'supplier';

    const VENDOR = 'vendor';

    const DELIVERY = 'delivery';

    const CLIENT = 'client';

    public static function toArray()
    {
        return [
            self::ADMIN => 'admin',
            self::SUPPLIER => 'supplier',
            self::VENDOR => 'vendor',
            self::DELIVERY => 'delivery',
            self::CLIENT => 'client',
        ];

    }
}
