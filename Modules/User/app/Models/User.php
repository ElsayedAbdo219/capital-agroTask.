<?php

namespace Modules\User\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\User\Enums\UserType;

// use Modules\User\Database\Factories\UserFactory;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
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
                case 'email':
                    $query->where('email', 'LIKE', "%{$value}%");
                    break;
                case 'type':
                    $query->whereIn('status',UserType::toArray());
                    break;
            }
        }

        return $query;
    }
}
