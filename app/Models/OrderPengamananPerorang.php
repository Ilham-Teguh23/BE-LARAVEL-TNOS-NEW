<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class OrderPengamananPerorang extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'order_pengamanan_perorangs';
    protected $guarded = ['id'];
    public $incrementing = false;
    
    protected static function booted(): void
    {
        static::creating(function (OrderPengamananPerorang $data) {
            $data->id = Str::uuid()->toString();
        });
    }
}
