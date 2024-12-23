<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pricepub extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'price_pub';

    public static function list_price($mitra) 
    {
        $data = Pricepub::where('mitra',$mitra)->get();
        return $data;
    }

    public static function get_price($mitra,$hours,$personil) 
    {
        $data = Pricepub::where('mitra',$mitra)
                    ->where('hours','LIKE',"%{$hours}%")
                    ->where('personil','LIKE',"%{$personil}%")
                    ->first();
        return $data;
    }
}
