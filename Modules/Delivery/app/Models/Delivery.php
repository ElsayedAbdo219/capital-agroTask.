<?php

namespace Modules\Delivery\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Models\User;
use Modules\Order\Models\Order;
use Modules\Delivery\Enums\OrderDeliveryStatus;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'deliver_id',
        'address',
        'phone',
        'status',
        'delivered_at',
    ];

    protected $casts = [
        'phone' => 'array',
        'delivered_at' => 'datetime',
    ];

    /* ======================
       Scopes
    ====================== */

    public function scopeOfAddress($query, $value)
    {
        return $query->where('address', 'LIKE', "%{$value}%");
    }

    public function scopeOfPhone($query, $value)
    {
        return $query->whereJsonContains('phone', $value);
    }

    public function scopeOfStatus($query, OrderDeliveryStatus|string $value)
    {
        return $query->where('status', $value);
    }

    /* ======================
       Relations
    ====================== */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function deliver()
    {
        return $this->belongsTo(User::class, 'deliver_id');
    }
}
