<?php

namespace Modules\ReturnProduct\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\OrderItem\Models\OrderItem;
use Modules\ReturnProduct\Enums\ReturnProductStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;

// use Modules\ReturnProduct\Database\Factories\ReturnProductFactory;

class ReturnProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'quantity',
        'reason',
    ];

    public function scopeFilter($query, array $filters)
    {
        foreach ($filters as $key => $value) {
            if (empty($value)) {
                continue;
            }
            switch ($key) {
                case 'quantity':
                    $query->where('name', 'LIKE', "%{$value}%");
                    break;
                case 'reason':
                    $query->where('reason', 'LIKE', "%{$value}%");
                    break;
                case 'status':
                    $query->whereIn('status',ReturnProductStatus::toArray());
                    break;
            }
        }

        return $query;
    }

    # Relations
    public function orderItem()
    {
      return $this->belongsTo(OrderItem::class);
    }
}
