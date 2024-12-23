<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OthersReference extends Model
{
    use HasFactory;

    protected $table = "pwa_others_reference";

    protected $guarded = [''];

    protected $keyType = "string";

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (OthersReference $others_reference) {
            $others_reference->id = Str::uuid()->toString();
        });
    }
}
