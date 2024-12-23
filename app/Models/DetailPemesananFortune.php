<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DetailPemesananFortune extends Model
{
    use HasFactory;

    protected $table = "detail_pemesanan_fortune";

    protected $guarded = [''];

    public $primaryKey = "id";

    protected $keyType = "string";

    public $incrementing = false;

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (DetailPemesananFortune $detailPemesananFortune) {
            $detailPemesananFortune->id = Str::uuid()->toString();
        });
    }
}
