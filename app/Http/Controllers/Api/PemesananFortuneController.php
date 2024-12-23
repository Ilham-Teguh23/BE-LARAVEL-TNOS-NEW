<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DetailPemesananFortune;
use App\Models\Order;
use App\Models\PemesananFortune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PemesananFortuneController extends Controller
{
    protected $b2b_orders, $pemesanan_fortune, $detail_pemesanan;

    public function __construct()
    {
        $this->b2b_orders = new Order();
        $this->pemesanan_fortune = new PemesananFortune();
        $this->detail_pemesanan = new DetailPemesananFortune();
    }

    public function getListPemesananFortune()
    {
        try {

            DB::beginTransaction();

            $pemesanan_fortune = $this->pemesanan_fortune->with(["b2b_orders:id,tnos_invoice_id,tnos_service_id,tnos_subservice_id,needs,external_id"])
                ->get();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Show Data Successfully",
                "data" => $pemesanan_fortune
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function storeListPemesananFortune(Request $request)
    {
        try {
            DB::beginTransaction();

            $pemesanan_fortune = $this->pemesanan_fortune->create($request->all());

            foreach ($request->fortune_id as $data) {
                $this->detail_pemesanan->create([
                    "pemesanan_fortune_id" => $pemesanan_fortune->id,
                    "fortune_id" => $data
                ]);
            }

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Create Data Successfully",
            ], 201);

        } catch (ValidationException $validation) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $validation
            ], 500);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function showPemesananFortune($id_pemesanan_fortune)
    {
        try {
            DB::beginTransaction();

            $pemesanan_fortune = $this->pemesanan_fortune->where("id", $id_pemesanan_fortune)
                ->with(["b2b_orders:id,tnos_invoice_id,tnos_service_id,tnos_subservice_id,needs,external_id"])
                ->first();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Show First Data Pemesanan Fortune Successfully",
                "data" => $pemesanan_fortune
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function updatePemesananFortune(Request $request, $id_pemesanan_fortune)
    {
        try {

            DB::beginTransaction();

            // $pemesanan_fortune = $this->pemesanan_fortune->where("id", $id_pemesanan_fortune)
            //     ->first();

            // $pemesanan_fortune->update($request->all());

            // foreach ($request->fortune_id as $data) {

            // }

            DB::commit();

            // return response()->json([
            //     "status" => true,
            //     "message" => "Show First Data Pemesanan Fortune Successfully",
            //     "data" => $pemesanan_fortune
            // ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function deletePemesananFortune($id_pemesanan_fortune)
    {
        try {

            DB::beginTransaction();

            $pemesanan_fortune = $this->pemesanan_fortune->where("id", $id_pemesanan_fortune)
                ->first();

            $detailPemesananFortune = $this->detail_pemesanan->where("pemesanan_fortune_id", $pemesanan_fortune->id)
                ->get();

            foreach ($detailPemesananFortune as $data) {
                $data->delete();
            }

            $pemesanan_fortune->delete();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Delete Pemesanan Fortune Successfully"
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }
}
