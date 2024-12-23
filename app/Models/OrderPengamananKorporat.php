<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class OrderPengamananKorporat extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'order_pengamanan_korporats';
    protected $guarded = ['id'];
    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (OrderPengamananKorporat $data) {
            $data->id = Str::uuid()->toString();
        });
    }
}
