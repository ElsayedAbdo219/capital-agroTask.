<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OtpAuthenticate extends Model
{
      use HasFactory;

    protected $fillable = ['email','otp','expiryDate'];
}
