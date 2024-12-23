<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TsaldoPointPayment extends Model
{
    use HasFactory;
    protected $table = 'tsaldo_point_payment';
    protected $guarded = ['id'];
    public $incrementing = false;
    
    protected static function booted(): void
    {
        static::creating(function (TsaldoPointPayment $data) {
            $data->id = Str::uuid()->toString();
        });
    }

}
