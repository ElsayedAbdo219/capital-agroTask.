<?php

namespace Modules\Product\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Stock\Models\Stock;

// use Modules\Product\Database\Factories\ProductFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'sku',
        'price',
        'tax',
        'additional_data',
        'feed_type',
        'animal_type',
        'weight_per_unit',
        'is_returnable',
    ];

    // Castings
    protected $casts = [
        'additional_data' => 'array',
        'is_returnable' => 'boolean',
    ];

  

    /* ======================
       Scopes
    ====================== */

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
                case 'description':
                    $query->where('description', 'LIKE', "%{$value}%");
                    break;
                case 'sku':
                    $query->where('sku', $value);
                    break;
                case 'price':
                    $query->where('price', $value);
                    break;
                case 'tax':
                    $query->where('tax', $value);
                    break;
                case 'additional_data':
                    $query->whereJsonContains('additional_data', $value);
                    break;
                case 'feed_type':
                    $query->where('feed_type', $value);
                    break;
                case 'animal_type':
                    $query->where('animal_type', $value);
                    break;
                case 'weight_per_unit':
                    $query->where('weight_per_unit', $value);
                    break;
            }
        }

        return $query;
    }

    public function stock()
    {
        return $this->hasOne(Stock::class);
    }
}
