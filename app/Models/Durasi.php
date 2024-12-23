<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Durasi extends Model
{
    use HasFactory;

    protected $table = "pwa_durasi";

    protected $guarded = [''];

    protected $keyType = "string";

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (Durasi $durasi) {
            $durasi->id = Str::uuid()->toString();
        });
    }

    public function layanans()
    {
        return $this->belongsTo(Layanan::class, "layanan_id", "id");
    }
}
