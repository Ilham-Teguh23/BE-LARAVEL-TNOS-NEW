<?php

namespace App\Http\Controllers\Carbon\Convert;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TimeConvertController extends Controller
{
    public function convertTimePaymentSuccess($date)
    {
        $dateString = $date;

        // Convert the date string to a Carbon instance
        $carbonDate = Carbon::parse($dateString)->setTimezone('Asia/Jakarta');

        // Format the date as "d/m/Y H:i T"
        $formattedDate = $carbonDate->format('d/m/Y H:i T');

        return $formattedDate;
    }
}
