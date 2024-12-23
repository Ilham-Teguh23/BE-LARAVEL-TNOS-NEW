<?php

namespace App\Http\Controllers\Api\PWARevamp;

use App\Http\Controllers\Controller;
use App\Models\Durasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DurasiController extends Controller
{
    protected $durasi;

    public function __construct()
    {
        $this->durasi = new Durasi();
    }

    public function index($layanan_id)
    {
        try {

            DB::beginTransaction();

            $durasi = $this->durasi->where("layanan_id", $layanan_id)->with("layanans")->get();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed Successfully",
                "data" => $durasi
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

            $this->durasi->create($request->all());

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

            $data = $this->durasi->findOrFail($id);

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

            $durasi = $this->durasi->findOrFail($id);

            $data = $request->all();

            $durasi->update($data);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed By Id Succesfully",
                "data" => $durasi
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

            $this->durasi->where("id", $id)->delete();

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

            $cek = $this->durasi->where("id", $id)->first();

            if ($cek->status == 1) {
                $cek->update([
                    "status" => "0"
                ]);
            } else {
                $cek->update([
                    "status" => "1"
                ]);
            }

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Success"
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
