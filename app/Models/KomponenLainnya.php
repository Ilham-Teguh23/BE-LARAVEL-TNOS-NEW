<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class KomponenLainnya extends Model
{
    use HasFactory;

    protected $table = "pwa_komponen_lainnya";

    protected $guarded = [''];

    protected $keyType = "string";

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (KomponenLainnya $othersComponent) {
            $othersComponent->id = Str::uuid()->toString();
        });
    }

    public function satuans()
    {
        return $this->belongsTo(Unit::class, "satuan", "id");
    }

    public function harga_komponen()
    {
        return $this->hasMany(HargaKomponen::class, "komponen_lainnya_id", "id");
    }
}
