<?php

namespace App\Http\Controllers\Api\PWARevamp;

use App\Http\Controllers\Controller;
use App\Models\Others;
use App\Models\OthersReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OthersController extends Controller
{
    protected $others, $others_reference;

    public function __construct()
    {
        $this->others = new Others();
        $this->others_reference = new OthersReference();
    }

    public function index($is_product_id)
    {
        try {

            DB::beginTransaction();

            $others = $this->others->where("url_id", $is_product_id)->get();

            foreach ($others as $data) {
                if ($data->is_product == "false") {
                    $data->load(["ref_sub_sections", "satuans"]);
                } else if ($data->is_product == "true") {
                    $data->load(["ref_sections", "satuans", "othersReferences"]);
                }
            }

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed Successfully",
                "data" => $others
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function store(Request $request)
    {
        try {

            DB::beginTransaction();

            $others = $this->others->create([
                "others_column" => $request->others_column,
                "url_id" => $request->url_id,
                "is_product" => $request->is_product,
                "harga" => $request->harga,
                "harga_dasar" => $request->harga_dasar,
                "include_tnos_fee" => $request->include_tnos_fee,
                "include_ppn" => $request->include_ppn,
                "tnos_fee" => $request->tnos_fee,
                "platform_fee" => $request->platform_fee,
                "satuan" => $request->satuan
            ]);

            if (!empty($request["ref_columns"])) {
                $ref_columns = explode(",", $request["ref_columns"]);

                foreach ($ref_columns as $column) {
                    $this->others_reference->create([
                        "reference_id" => $column,
                        "others_id" => $others->id
                    ]);
                }
            }

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Saved Successfully"
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function show($id)
    {
        try {

            DB::beginTransaction();

            $data = $this->product->with("sections")->findOrFail($id);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed By Id Succesfully",
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

    public function update(Request $request, $id)
    {
        try {

            DB::beginTransaction();

            $product = $this->product->findOrFail($id);

            $data = $request->all();

            $product->update($data);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed By Id Succesfully",
                "data" => $product
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {

            DB::beginTransaction();

            $others = $this->others->where("id", $id)->first();

            $this->others_reference->where("others_id", $others->id)->delete();

            $others->delete();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Delete Succesfully"
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function changeStatus($id)
    {
        try {

            DB::beginTransaction();

            $product = $this->product->where("id", $id)->first();

            if ($product->status == "1") {
                $product->update([
                    "status" => "0"
                ]);
            } else if ($product->status == "0") {
                $product->update([
                    "status" => "1"
                ]);
            }

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Berhasil di Simpan",
                "data" => $product
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
