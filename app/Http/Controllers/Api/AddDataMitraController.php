<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddDataMitraController extends Controller
{
    public function addDataMitra(Request $request,$id)
    {
        $order = Order::find($id);

        if(!$order){
            return response()->json([
                'success' => false,
                'message' => 'ID not found',
            ], 404);
        }

        DB::beginTransaction();
        try {
            $order->dataMitra = json_encode($request->dataMitra);
            $order->update();
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Partner Data Successfully added',
                'detail' => $order,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong (Try Catch)',
                'error' => $e,
            ], 500);

        }
    }
}
