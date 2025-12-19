<?php

namespace Modules\Order\Models;

use Modules\User\Models\User;
use Modules\Order\Enums\OrderStatus;
use Illuminate\Database\Eloquent\Model;
use Modules\OrderItem\Models\OrderItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_amount',
        'status',
    ];

    /* ======================
       Scopes
    ====================== */

    public function scopeOfUser($query, $value)
    {
        return $query->whereHas('user', function ($q) use ($value) {
            $q->where('name', 'LIKE', "%{$value}%")
              ->orWhere('email', 'LIKE', "%{$value}%");
        });
    }

    public function scopeOfStatus($query, OrderStatus|string $value)
    {
        return $query->where('status', $value);
    }

    /* ======================
       Relations
    ====================== */

    public function user()
    {
        return $this->belongsTo(User::class);
    }

      public function orderItem()
    {
        return $this->belongsTo(OrderItem::class ,  'id' , 'order_id');
    }
}
