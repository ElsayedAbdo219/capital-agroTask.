<?php

namespace Modules\Payment\Models;

use Modules\Order\Models\Order;
use Illuminate\Database\Eloquent\Model;
use Modules\Payment\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
// use Modules\Payment\Database\Factories\PaymentFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'method',
    ];

    /* ======================
       Scopes
    ====================== */

    public function scopeOfMethod($query, PaymentMethod|string $value)
    {
        return $query->where('method', 'LIKE', "%{$value}%");
    }

    public function scopeOfAmount($query, $value)
    {
        return $query->where('amount', $value);
    }

    /* ======================
       Relations
    ====================== */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
    
}
