<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserVerify extends Model
{
    use HasFactory;
    public $table = "users_verify";
    protected $guarded = [
       'id'
    ];

    public $incrementing = false;
    
    protected static function booted(): void
    {
        static::creating(function (UserVerify $user) {
            $user->id = Str::uuid()->toString();
        });
    }
}
