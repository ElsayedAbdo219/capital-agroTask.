<?php

namespace Modules\Stock\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Product\Models\Product;
use Modules\Warehouse\Models\Warehouse;

// use Modules\Stock\Database\Factories\StockFactory;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'warehouse_id',
        'quantity',
        'batch_no',
        'expiry_date',
    ];

    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $key => $value) {
            if (empty($value)) {
                continue;
            }
            switch ($key) {
                case 'quantity':
                    $query->where('quantity', 'LIKE', "%{$value}%");
                    break;
                case 'batch_no':
                    $query->where('batch_no', 'LIKE', "%{$value}%");
                    break;
                case 'expiry_date':
                    $query->where('expiry_date', 'LIKE', "%{$value}%");
                    break;

            }
        }

        return $query;
    }

    // Relations
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
}
