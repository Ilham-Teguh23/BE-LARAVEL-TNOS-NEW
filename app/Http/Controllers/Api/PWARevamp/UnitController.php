<?php

namespace App\Http\Controllers\Api\PWARevamp;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UnitController extends Controller
{
    protected $unit;

    public function __construct()
    {
        $this->unit = new Unit();
    }

    public function index()
    {
        try {

            DB::beginTransaction();

            $data["unit"] = $this->unit->orderBy("created_at", "DESC")->get();

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

            $data = $this->unit->create([
                "satuan" => empty($request->satuan) ? "" : $request->satuan,
                "slug" => empty($request->satuan) ? "" : Str::slug($request->satuan),
                "status" => 1
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Saved Successfully",
                "data" => $data
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Data Saved Successfully',
                'data' => $data
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Data Save Failed',
                'error' => $e->getMessage()
            ]);
        }
    }

    public function edit($id)
    {
        try {

            DB::beginTransaction();

            $edit = $this->unit->where("id", $id)->first();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed By Id Succesfully",
                "data" => $edit
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

            $unit = $this->unit->findOrFail($id);

            $data = $request->all();

            $data["slug"] = Str::slug($data["satuan"]);

            $unit->update($data);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Update Successfully",
                "data" => $unit
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

            $unit = $this->unit->where("id", $id)->first();
            $unit->delete();

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

            $cek = $this->unit->where("id", $id)->first();

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
