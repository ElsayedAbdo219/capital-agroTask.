<?php

namespace Modules\User\Models;

use Modules\User\Enums\UserType;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

// use Modules\User\Database\Factories\UserFactory;

class User extends Authenticatable
{
    use HasFactory , HasApiTokens ;

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
