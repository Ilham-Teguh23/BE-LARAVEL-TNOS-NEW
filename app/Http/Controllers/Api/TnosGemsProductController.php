<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TnosGems;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TnosGemsProductController extends Controller
{
    public function index(){
        $product = DB::table('tnos_gems')
        ->orderBy('tnos_gems.created_at', 'DESC')->get();

        return response()->json([
            'message' => 'Data Product',
            'product' => $product,
        ], 200);
    }

    public function store(Request $request){

        $validator = Validator::make(
            $request->all(),
            [
                'name_product' => 'required',
                'harga' => 'required',
                'point_token' => 'required',
                'status' => 'required|in:active,non_active',
            ]
        );
        if ($validator->fails()) {
            return response()->json(
                [
                    'message' => $validator->errors(),
                ],
                401
            );
        }

        $dataProduct = [
            'name_product' => $request->name_product,
            'harga' => $request->harga,
            'point_token' => $request->point_token,
            'status' => $request->status,
        ];

        $product = TnosGems::create($dataProduct);



        return response()->json([
            'message' => 'Data added successfully',
            'product' => $product,
        ], 200);
    }
}
