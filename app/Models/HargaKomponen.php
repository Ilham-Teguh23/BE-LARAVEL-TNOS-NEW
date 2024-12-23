<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class HargaKomponen extends Model
{
    use HasFactory;

    protected $table = "pwa_harga_komponen";

    protected $guarded = [''];

    protected $keyType = "string";

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (HargaKomponen $hargaKomponen) {
            $hargaKomponen->id = Str::uuid()->toString();
        });
    }
}
