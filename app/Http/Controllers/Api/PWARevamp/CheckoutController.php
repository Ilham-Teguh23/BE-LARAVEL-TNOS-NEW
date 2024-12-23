<?php

namespace App\Http\Controllers\Api\PWARevamp;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use App\Models\Section;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected $layanan, $section;

    public function __construct()
    {
        $this->layanan = new Layanan();
        $this->section = new Section();
    }

    public function index($layanan_id)
    {
        try {

            DB::beginTransaction();

            $data["layanan"] = $this->layanan->where("id", $layanan_id)->first();
            $data["section"] = $this->section->where("layanan_id", $data["layanan"]["id"])->get();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed Successfully",
                "data" => $data
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }
}
