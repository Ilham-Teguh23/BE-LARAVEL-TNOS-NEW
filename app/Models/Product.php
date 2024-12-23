<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $table = "pwa_product";

    protected $guarded = [''];

    protected $keyType = "string";

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            $product->id = Str::uuid()->toString();
        });
    }

    public function sections()
    {
        return $this->belongsTo(Section::class, "section_id");
    }

    public function subsections()
    {
        return $this->belongsTo(SubSection::class, "section_id", "id");
    }

    public function satuans()
    {
        return $this->belongsTo(Unit::class, "satuan", "id");
    }
}
