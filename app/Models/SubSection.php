<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SubSection extends Model
{
    use HasFactory;

    protected $table = "pwa_sub_section";

    protected $guarded = [''];

    protected $keyType = "string";

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (SubSection $subsection) {
            $subsection->id = Str::uuid()->toString();
        });
    }

    public function sections()
    {
        return $this->belongsTo(Section::class, "section_id");
    }
}
