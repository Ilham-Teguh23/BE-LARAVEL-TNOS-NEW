<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Fortune extends Model
{
    use HasFactory;

    protected $table = "fortune";

    protected $guarded = [''];

    public $primaryKey = "id";

    protected $keyType = "string";

    public $incrementing = false;

    public $timestamps = false;

    protected static function booted(): void
    {
        static::creating(function (Fortune $fortune) {
            $fortune->id = Str::uuid()->toString();
        });
    }
}
