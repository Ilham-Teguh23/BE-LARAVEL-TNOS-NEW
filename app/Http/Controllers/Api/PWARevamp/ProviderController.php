<?php

namespace App\Http\Controllers\Api\PWARevamp;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ProviderController extends Controller
{
    protected $provider;

    public function __construct()
    {
        $this->provider = new Provider();
    }

    public function index()
    {
        try {

            DB::beginTransaction();

            $data["provider"] = $this->provider->orderBy("created_at", "ASC")->get();

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
            $data = $request->all();

            $data["slug"] = Str::slug($data["name_sc"]);
            $data["status"] = 1;

            $imagePath = "";
            if ($request->hasFile('image')) {
                $imageName = rand(10000000, 99999999) . '.' . $request->file("image")->getClientOriginalExtension();
                $path = public_path('images/SC/');
                $imagePath = url('images/SC/') . '/' . $imageName;
                $request->file("image")->move($path, $imageName);

                $data['image_name'] = $imageName;
                $data['image_url'] = $imagePath;
                $data['mime'] = $request->file('image')->getClientMimeType();
            }

            $data["image"] = $imagePath;

            $this->provider->create($data);

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

    public function show($id)
    {
        try {

            DB::beginTransaction();

            $data = $this->provider->where("id", $id)->first();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed By Id Successfully",
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

            $provider = $this->provider->findOrFail($id);

            $data = $request->all();

            $data["slug"] = Str::slug($data["name_sc"]);
            $data["status"] = 1;

            $imagePath = "";
            if ($request->hasFile('image')) {
                $imageName = rand(10000000, 99999999) . '.' . $request->file("image")->getClientOriginalExtension();
                $path = public_path('images/SC/');
                $imagePath = url('images/SC/') . '/' . $imageName;
                $request->file("image")->move($path, $imageName);

                $data['image_name'] = $imageName;
                $data['image_url'] = $imagePath;
                $data['mime'] = $request->file('image')->getClientMimeType();

                if (!empty($provider->image)) {

                    $pathImage = $provider->image;
                    $path = parse_url($pathImage, PHP_URL_PATH);
                    $relativePath = ltrim($path, "/");

                    $oldImagePath = public_path($relativePath);

                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }
            }

            $data["image"] = $imagePath;

            $provider->update($data);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Update Successfully",
                "data" => $provider
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

            $provider = $this->provider->findOrFail($id);

            $imageName = basename($provider->image);

            $imagePath = public_path('images/SC/') . $imageName;

            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            $provider->delete();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Delete Successfully"
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

            $cek = $this->provider->where("id", $id)->first();

            if ($cek->status == "1") {
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
