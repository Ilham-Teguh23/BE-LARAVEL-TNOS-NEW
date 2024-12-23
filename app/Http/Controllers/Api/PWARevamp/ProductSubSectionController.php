<?php

namespace App\Http\Controllers\Api\PWARevamp;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductSubSectionController extends Controller
{
    protected $product;

    public function __construct()
    {
        $this->product = new Product();
    }

    public function index($section_id)
    {
        try {

            DB::beginTransaction();

            $products = $this->product->where("section_id", $section_id)->with("subsections")->get();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed Successfully",
                "data" => $products
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

            $data = $request->all();

            if ($data["setting"] == "1") {
                $data["setting"] = "include-tnos";
            } else if ($data["setting"] == "2") {
                $data["setting"] = "harga-dasar";
            }

            $this->product->create($data);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Saved Successfully",
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

    public function show($id)
    {
        try {

            DB::beginTransaction();

            $data = $this->product->findOrFail($id);

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

            $product = $this->product->where("id", $id)->first();
            $product->delete();

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
