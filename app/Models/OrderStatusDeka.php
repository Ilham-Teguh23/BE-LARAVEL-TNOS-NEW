<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusDeka extends Model
{
    use HasFactory;
    protected $table = 'order_status_deka';
    protected $fillable = [
        'id',
        'id_order',
        'status',
        'datetime',
        'deskripsi'
    ];
}
