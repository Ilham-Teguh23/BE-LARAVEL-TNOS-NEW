<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use PDF;
use Illuminate\Http\Request;

class CustomPrintController extends Controller
{
    public function custom_print(Request $request)
    {
        $content = $request->content;

        // dd($content["service_datas"]['id']);
        // $cons = json_decode($content["alamat_badan_hukum"], true);
        // dd($cons['domisili_sekarang']);
        // return view("custom-print", compact("content"));

        // die();
        $pdf = PDF::loadView("custom-print", ["content" => $content]);

        $pdf->setPaper('a4', 'portrait');
        return $pdf->download("DATA_PRINT.pdf");
    }
}
