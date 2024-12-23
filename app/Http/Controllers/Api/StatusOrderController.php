<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
// use App\Http\Controllers\Api\MessageUserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatusOrderController extends Controller
{
    public function gettingCorporatePartners($id)
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
        $order->status_order = "003";
        $order->update();

        // $dataMessage = [
        //     'title' => 'Mitra telah tersedia',
        //     'description' => 'Selamat '.$order->name.', Mitra Pengamanan Korporat kamu dengan No Pesanan '.$order->tnos_invoice_id.' telah tersedia ',
        // ];

        // $message = new MessageUserController;
        // $message->generateMessage($order->user_id,$order->id,$dataMessage , $order->status_order);
        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data name: '. $order->name.' updated status order to Getting Corporate Partners successfully',
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

    public function goToLocation($id)
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
        $order->status_order = "004";
        $order->update();

        // $dataMessage = [
        //     'title' => 'Mitra Menuju Lokasi',
        //     'description' => 'Mitra Pengamanan Korporat dengan No Pesanan '.$order->tnos_invoice_id.' akan menuju lokasimu',
        // ];

        // $message = new MessageUserController;
        // $message->generateMessage($order->user_id,$order->id,$dataMessage , $order->status_order);
        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data name: '. $order->name.' updated status order to Go To Location successfully',
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

    public function onDuty($id)
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
        $order->status_order = "005";
        $order->update();


        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data name: '. $order->name.' updated status order to On duty successfully',
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

    public function documentCheck($id)
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
        $order->status_order = "006";
        $order->update();


        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data name: '. $order->name.' updated status order to Document Check successfully',
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

    public function documentRegistration($id)
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
        $order->status_order = "007";
        $order->update();


        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data name: '. $order->name.' updated status order to Document Registration successfully',
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

    public function registrationDone($id)
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
        $order->status_order = "008";
        $order->update();


        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data name: '. $order->name.' updated status order to Registration Done successfully',
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

    public function documentDelivery($id)
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
        $order->status_order = "009";
        $order->update();


        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data name: '. $order->name.' updated status order to Document Delivery successfully',
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


    public function finish($id)
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
        $order->status_order = "010";
        $order->update();

        // $dataMessage = [
        //     'title' => 'Selamat pesanan anda telah selesai',
        //     'description' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Aspernatur reiciendis itaque in id vel rerum.',
        // ];

        // $message = new MessageUserController;
        // $message->generateMessage($order->user_id,$order->id,$dataMessage , $order->status_order);
        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Data name: '. $order->name.' updated status order to Finish successfully',
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
