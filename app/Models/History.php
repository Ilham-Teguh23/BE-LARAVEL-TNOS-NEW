<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class History extends Model
{
    use HasFactory;

    protected $table = "pwa_history";

    protected $guarded = [''];

    protected $keyType = "string";

    public $incrementing = false;

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (History $history) {
            $history->id = Str::uuid()->toString();
        });
    }

    public function layanan()
    {
        return $this->belongsTo(Layanan::class, "id_layanan", "id");
    }
}
