<?php

namespace App\Http\Controllers\Number\Convert;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NumberConvertController extends Controller
{
    public function convertNumberPrice($price)
    {
        $number = $price;

        $formattedNumber = number_format($number, 2, ',', '.');

        return  $formattedNumber;
    }
}
