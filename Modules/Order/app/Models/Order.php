<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Order\Enums\OrderStatus;
use Modules\User\Models\User;

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
}
