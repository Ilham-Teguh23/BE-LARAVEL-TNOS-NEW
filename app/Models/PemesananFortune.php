<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PemesananFortune extends Model
{
    use HasFactory;

    protected $table = "pemesanan_fortune";

    protected $guarded = [''];

    public $primaryKey = "id";

    protected $keyType = "string";

    public $incrementing = false;

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (PemesananFortune $pemesananFortune) {
            $pemesananFortune->id = Str::uuid()->toString();
        });
    }

    public function b2b_orders()
    {
        return $this->belongsTo(Order::class, "b2b_id");
    }
}
