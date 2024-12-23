<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FormCatatan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FormCatatanController extends Controller
{
    protected $form_catatan;

    public function __construct()
    {
        $this->form_catatan = new FormCatatan();
    }

    public function getListCatatan()
    {
        try {

            DB::beginTransaction();

            $catatan = $this->form_catatan
                ->with(["b2b_orders:id,tnos_invoice_id,tnos_service_id,tnos_subservice_id,external_id,invoice_id"])
                ->get();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Show Data Successfully",
                "data" => $catatan
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function storeCatatan(Request $request)
    {
        try {

            DB::beginTransaction();

            $catatan = $this->form_catatan->create($request->all());

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Create Data Successfully",
                "data" => $catatan
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function showCatatan($id_catatan)
    {
        try {

            DB::beginTransaction();

            $catatan = $this->form_catatan->where("id", $id_catatan)
                ->with(["b2b_orders:id,tnos_invoice_id,tnos_service_id,tnos_subservice_id,external_id,invoice_id"])
                ->first();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Show First Data Catatan Successfully",
                "data" => $catatan
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function deleteCatatan($id_catatan)
    {
        try {

            DB::beginTransaction();

            $this->form_catatan->where("id", $id_catatan)->delete();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Delete Catatan Successfully"
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
