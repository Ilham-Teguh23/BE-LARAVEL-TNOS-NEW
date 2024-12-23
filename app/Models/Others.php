<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Others extends Model
{
    use HasFactory;

    protected $table = "pwa_others";

    protected $guarded = [''];

    protected $keyType = "string";

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (Others $others) {
            $others->id = Str::uuid()->toString();
        });
    }

    public function ref_sub_sections()
    {
        return $this->belongsTo(SubSection::class, "url_id");
    }

    public function ref_sections()
    {
        return $this->belongsTo(Section::class, "url_id", "id");
    }

    public function othersReferences()
    {
        return $this->hasMany(OthersReference::class, "others_id", "id");
    }

    public function satuans()
    {
        return $this->belongsTo(Unit::class, "satuan", "id");
    }
}
