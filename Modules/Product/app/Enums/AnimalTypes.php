<?php

namespace Modules\Product\Enums;

enum AnimalTypes
{
   const POULTRY = 'poultry';
    const CATTLE = 'cattle';
    const FISH = 'fish';

    public static function toArray()
    {
        return [
            self::POULTRY => 'poultry',
            self::CATTLE => 'cattle',
            self::FISH => 'fish',
        ];
    }
}
