<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TsaldoPointHistories extends Model
{
    use HasFactory;
    protected $table = 'tsaldo_point_histories';
    protected $guarded = ['id'];
    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (TsaldoPointHistories $data) {
            $data->id = Str::uuid()->toString();
        });
    }
}
