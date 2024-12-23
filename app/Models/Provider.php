<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Provider extends Model
{
    use HasFactory;

    protected $table = "pwa_provider";

    protected $guarded = [''];

    protected $keyType = "string";

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (Provider $provider) {
            $provider->id = Str::uuid()->toString();
        });
    }
}
