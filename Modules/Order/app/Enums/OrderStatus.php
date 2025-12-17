<?php

namespace Modules\Order\Enums;

enum OrderStatus {
    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const SHIPPED = 'shipped';

    public static function toArray()
    {
        return [
            self::PENDING => 'pending',
            self::PROCESSING => 'processing',
            self::SHIPPED => 'shipped',
        ];
        
    }

}
