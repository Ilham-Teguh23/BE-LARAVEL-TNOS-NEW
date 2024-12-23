<?php

namespace App\Http\Controllers\Api\PwaRevamp;

use App\Http\Controllers\Controller;
use App\Models\HargaKomponen;
use App\Models\KomponenLainnya;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class KomponenLainnyaController extends Controller
{
    protected $othersComponent, $hargaKomponen;

    public function __construct()
    {
        $this->othersComponent = new KomponenLainnya();
        $this->hargaKomponen = new HargaKomponen();
    }

    public function index()
    {
        try {

            DB::beginTransaction();

            $data["othersComponent"] = $this->othersComponent->with(["satuans"])->orderBy("created_at", "DESC")->get();

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

            $komponen = $this->othersComponent->create([
                "komponen" => $request->komponen,
                "slug" => Str::slug($request->komponen),
                "satuan" => $request->satuan
            ]);

            foreach ($request->hargaDari as $index => $hargaDari) {
                $this->hargaKomponen->create([
                    "komponen_lainnya_id" => $komponen["id"],
                    "harga_dari" => $hargaDari,
                    "harga_sampai" => $request->hargaSampai[$index],
                    "harga_akhir" => $request->hargaAkhir[$index],
                    "status" => 1
                ]);
            }

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Saved Successfully"
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

            $edit = $this->othersComponent->where("id", $id)->with("harga_komponen")->first();

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

            $unit = $this->othersComponent->findOrFail($id);

            $priceKomponen = $this->hargaKomponen->where("komponen_lainnya_id", $id)->get();

            $existingId = $priceKomponen->pluck("id")->toArray();

            foreach ($request->id_harga_komponen as $index => $hargaKomponenId) {
                if (in_array($hargaKomponenId, $existingId)) {
                    $komponen = $this->hargaKomponen->find($hargaKomponenId);
                    if ($komponen) {
                        $komponen->update([
                            'harga_dari' => $request->harga_dari[$index],
                            'harga_sampai' => $request->harga_sampai[$index],
                            'harga_akhir' => $request->harga_akhir[$index]
                        ]);
                    }
                } else {
                    $this->hargaKomponen->create([
                        'komponen_lainnya_id' => $id,
                        'harga_dari' => $request->harga_dari[$index],
                        'harga_sampai' => $request->harga_sampai[$index],
                        'harga_akhir' => $request->harga_akhir[$index],
                        'status' => 1
                    ]);
                }
            }

            $unit->update([
                "komponen" => $request->komponen,
                "slug" => Str::slug($request->komponen),
                "satuan" => $request->satuan
            ]);

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

            $unit = $this->othersComponent->where("id", $id)->first();

            $this->hargaKomponen->where("komponen_lainnya_id", $unit->id)->delete();

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

            $cek = $this->othersComponent->where("id", $id)->first();

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
