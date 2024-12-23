<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fortune;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class FortuneController extends Controller
{
    protected $fortune;

    public function __construct()
    {
        $this->fortune = new Fortune();
    }

    public function getListFortune()
    {
        try {
            DB::beginTransaction();

            $fortune = $this->fortune->get();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Show Data Successfully",
                "data" => $fortune
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function storeFortune(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "nik" => "required|string|max:50",
            "npwp" => "required|string|max:50",
            "nama" => "required|string|max:50",
            "email" => "required|string",
            "nomor_hp" => "required|string|max:30",
            "tempat_lahir" => "required",
            "tanggal_lahir" => "required",
            "jenis_kelamin" => "required",
            "domisili" => "required",
            "provinsi" => "required",
            "kab_kota" => "required",
            "kecamatan" => "required",
            "kelurahan" => "required",
            "tanggal_mendaftar" => "required",
            "jam_mendaftar" => "required",
            "pendaftar_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Error Validation",
                "errors" => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $fortune = $this->fortune->create($request->all());

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Create Data Successfully",
                "data" => $fortune
            ], 201);

        } catch (ValidationException $validation) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $validation
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function showFortune($id_fortune)
    {
        try {

            DB::beginTransaction();

            $fortune = $this->fortune->where("id", $id_fortune)
                ->first();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Show First Data Fortune Successfully",
                "data" => $fortune
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function updateFortune(Request $request, $id_fortune)
    {
        $validator = Validator::make($request->all(), [
            "nik" => "required|string|max:50",
            "npwp" => "required|string|max:50",
            "nama" => "required|string|max:50",
            "email" => "required|string",
            "nomor_hp" => "required|string|max:30",
            "tempat_lahir" => "required",
            "tanggal_lahir" => "required",
            "jenis_kelamin" => "required",
            "domisili" => "required",
            "provinsi" => "required",
            "kab_kota" => "required",
            "kecamatan" => "required",
            "kelurahan" => "required",
            "tanggal_mendaftar" => "required",
            "jam_mendaftar" => "required",
            "pendaftar_id" => "required",
        ]);

        if ($validator->fails()) {
            return response()->json([
                "status" => false,
                "message" => "Error Validation",
                "errors" => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            $fortune = $this->fortune->where("id", $id_fortune)->first();

            $fortune->update($request->all());

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Update Data Successfully",
                "data" => $fortune
            ], 201);

        } catch (ValidationException $validation) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $validation
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function deleteFortune($id_fortune)
    {
        try {

            DB::beginTransaction();

            $this->fortune->where("id", $id_fortune)
                ->delete();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Delete Fortune Successfully"
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    public function changeStatus($id_fortune)
    {
        try {

            DB::beginTransaction();

            $fortune = $this->fortune->where("id", $id_fortune)
                ->first();

            if ($fortune->status == "1") {
                $fortune->update([
                    "status" => "0"
                ]);
            } else if ($fortune->status == "0") {
                $fortune->update([
                    "status" => "1"
                ]);
            }

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Change Status Fortune Successfully",
                "data" => $fortune
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
