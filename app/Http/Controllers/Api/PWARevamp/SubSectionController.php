<?php

namespace App\Http\Controllers\Api\PWARevamp;

use App\Http\Controllers\Controller;
use App\Models\SubSection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SubSectionController extends Controller
{
    protected $subsection;

    public function __construct()
    {
        $this->subsection = new SubSection();
    }

    public function index($section_id)
    {
        try {

            DB::beginTransaction();

            $data["subsection"] = $this->subsection->where("section_id", $section_id)->with("sections")->get();

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

            $this->subsection->create($data);

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

            $data = $this->subsection->with("sections")->findOrFail($id);

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

            $subsection = $this->subsection->findOrFail($request->id);

            $data = $request->all();

            $data["slug"] = Str::slug($data["name"]);

            $subsection->update($data);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed By Id Succesfully",
                "data" => $subsection
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

            $subsection = $this->subsection->where("id", $id)->first();
            $subsection->delete();

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
