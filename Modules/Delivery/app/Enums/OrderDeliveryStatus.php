<?php

namespace Modules\Delivery\Enums;

enum OrderDeliveryStatus
{
    const PENDING = 'pending';

    const SHIPPED = 'shipped';

    const DELIVERED = 'delivered';

    public static function toArray()
    {
        return [
            self::PENDING => 'pending',
            self::SHIPPED => 'shipped',
            self::DELIVERED => 'delivered',
        ];
        
    }


}
