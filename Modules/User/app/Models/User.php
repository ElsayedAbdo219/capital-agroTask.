<?php

namespace Modules\User\Models;

use Modules\Order\Models\Order;
use App\Traits\ApiResponseTrait;
use Modules\User\Enums\UserType;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

// use Modules\User\Database\Factories\UserFactory;

class User extends Authenticatable
{
    use HasApiTokens , HasFactory, ApiResponseTrait;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
    ];

  
   
    # Scopes
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
                    $query->whereIn('status', UserType::toArray());
                    break;
            }
        }

        return $query;
    }
   # Relations
    public function orders()
    {
        return $this->hasMany(Order::class);
    }


      // function inside model
    protected static function CheckOnThisUser($user) 
    {
         if(!$user)
        return self::respondWithErrors('User Not Found');
    }
}
