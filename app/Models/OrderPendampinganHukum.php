<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
class OrderPendampinganHukum extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'order_pendampingan_hukums';
    protected $guarded = ['id'];
    public $incrementing = false;
    
    protected static function booted(): void
    {
        static::creating(function (OrderPendampinganHukum $data) {
            $data->id = Str::uuid()->toString();
        });
    }
}
