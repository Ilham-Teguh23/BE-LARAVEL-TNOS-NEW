<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class OrderDetail extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'order_details';
    protected $guarded = ['id'];
    public $incrementing = false;
    
    protected static function booted(): void
    {
        static::creating(function (OrderDetail $data) {
            $data->id = Str::uuid()->toString();
        });
    }

}
