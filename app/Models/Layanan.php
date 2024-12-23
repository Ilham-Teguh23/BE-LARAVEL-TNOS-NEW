<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Layanan extends Model
{
    use HasFactory;

    protected $table = "pwa_layanan";

    protected $guarded = [''];

    protected $keyType = "string";

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (Layanan $layanan) {
            $layanan->id = Str::uuid()->toString();
        });
    }

    public function providers()
    {
        return $this->belongsTo(Provider::class, "provider_id");
    }

    public function durasi()
    {
        return $this->hasOne(Durasi::class, "layanan_id", "id");
    }
}
