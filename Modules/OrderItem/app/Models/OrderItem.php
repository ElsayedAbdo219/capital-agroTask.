<?php

namespace Modules\OrderItem\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Product\Models\Product;
use Modules\Order\Models\Order;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'total_price'
    ];

    /* ======================
       Scopes
    ====================== */

    public function scopeOfProduct($query, $value)
    {
        return $query->whereHas('product', function ($q) use ($value) {
            $q->where('name', 'LIKE', "%{$value}%")
              ->orWhere('description', 'LIKE', "%{$value}%");
        });
    }

    /* ======================
       Relations
    ====================== */

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
