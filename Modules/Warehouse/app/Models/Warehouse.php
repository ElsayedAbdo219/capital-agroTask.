<?php

namespace Modules\Warehouse\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

// use Modules\Warehouse\Database\Factories\WarehouseFactory;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $key => $value) {
            if (empty($value)) {
                continue;
            }
            switch ($key) {
                case 'name':
                    $query->where('name', 'LIKE', "%{$value}%");
                    break;
                case 'location':
                    $query->where('location', 'LIKE', "%{$value}%");
                    break;
            }
        }

        return $query;
    }
}
