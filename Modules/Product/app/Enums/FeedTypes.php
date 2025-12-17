<?php

namespace Modules\Product\Enums;

enum FeedTypes
{
    const STARTER = 'starter';
    const GROWER = 'grower';
    const FINISHER = 'finisher';

    public static function toArray()
    {
        return [
            self::STARTER => 'starter',
            self::GROWER => 'grower',
            self::FINISHER => 'finisher',
        ];
    }
}
