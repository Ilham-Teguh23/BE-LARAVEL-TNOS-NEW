<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\OrderStatusDeka;

class OrderTAB extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'tab_subscribe_detail';

    protected $keyType = "string";

    protected $fillable = [
        'id_subscribe', 'external_id', 'start_date', 'end_date', 'isTrial', 'status_subscribe', 'status_transaksi', 'amount', 'timestamp', 'id_user', 'id_master_paket', 'name', 'phone_number', 'member_code'
    ];

    public $incrementing = false;

    protected static function booted(): void
    {
        static::creating(function (Order $data) {
            $data->id = Str::uuid()->toString();
        });
    }
}
