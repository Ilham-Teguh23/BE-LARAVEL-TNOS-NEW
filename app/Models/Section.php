<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Section extends Model
{
    use HasFactory;

    protected $table = "pwa_section";

    protected $guarded = [''];

    protected $keyType = "string";

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (Section $section) {
            $section->id = Str::uuid()->toString();
        });
    }

    public function durations()
    {
        return $this->belongsTo(Durasi::class, "durasi_id");
    }
}
