<?php

namespace App\Http\Controllers\Api\PWARevamp;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class LayananController extends Controller
{
    protected $layanan;

    public function __construct()
    {
        $this->layanan = new Layanan();
    }

    public function index($provider_id)
    {
        try {

            DB::beginTransaction();

            $data["layanan"] = $this->layanan->where("provider_id", $provider_id)->with("providers")->get();

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

    public function store(Request $request)
    {
        try {

            DB::beginTransaction();

            $data = $request->all();

            $data["slug"] = Str::slug($data["name"]);

            $this->layanan->create($data);

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

            $data = $this->layanan->with("providers")->findOrFail($id);

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

            $layanan = $this->layanan->findOrFail($id);

            $data = $request->all();

            $data["slug"] = Str::slug($data["name"]);

            $layanan->update($data);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed By Id Succesfully",
                "data" => $layanan
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

            $layanan = $this->layanan->where("id", $id)->first();
            $layanan->delete();

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
}
