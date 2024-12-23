<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Masterprice extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'master_price';
}
