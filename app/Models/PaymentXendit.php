<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class PaymentXendit extends Model
{
    use HasFactory;
    protected $table = 'payment_xendits';
    protected $guarded = ['id'];

    // public $incrementing = false;

    // protected static function booted(): void
    // {
    //     static::creating(function (PaymentXendit $data) {
    //         $data->id = Str::uuid()->toString();
    //     });
    // }
}
