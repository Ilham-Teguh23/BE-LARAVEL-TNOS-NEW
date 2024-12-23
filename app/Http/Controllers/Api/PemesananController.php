<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PemesananController extends Controller
{
    protected $b2b;

    public function __construct()
    {
        $this->b2b = new Order();
    }

    public function updateStatus(Request $request, $b2b_id)
    {
        try {

            DB::beginTransaction();

            $ordersB2b = $this->b2b->where("id", $b2b_id)->first();

            $ordersB2b->status_order = $request->status_order;

            if ($request->status_order === "010") {
                $ordersB2b->finished_at = date("Y-m-d H:i:s");
            }

            $ordersB2b->update();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Update Data Successfully"
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function getPesanan($b2b_id)
    {
        try {

            DB::beginTransaction();

            $data = $this->b2b->where("id", $b2b_id)->first();

            DB::commit();

            return response()->json([
                "status" => empty($data) ? false : true,
                "data" => $data
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function updatePembayaran(Request $request)
    {
        try {

            DB::beginTransaction();

            $orders = $this->b2b->where("id", $request->id)->update([
                "no_referensi" => $request->no_referensi,
                "date_paid_vendor" => $request->date_paid_vendor,
                "note_paid_vendor" => $request->note_paid_vendor,
                "bank_paid_vendor" => $request->bank_paid_vendor,
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Update Successfully",
                "data" => $orders
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function getNoReferensi($id)
    {
        try {

            DB::beginTransaction();

            $orders = $this->b2b->where("id", $id)->first();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Showed By ID Successfully",
                "data" => $orders
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function updateLaporan(Request $request, $id)
    {
        try {

            DB::beginTransaction();

            $data = $this->b2b->where("id", $id)->update([
                "report_status" => $request->report_status,
                "notes" => $request->notes ? $request->notes : "",
                "status_order" => "010"
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Update Laporan Success",
                "data" => $data
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
