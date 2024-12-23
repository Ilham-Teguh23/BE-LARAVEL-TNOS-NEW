<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TsaldoPointHistories;
use App\Models\TsaldoPointPayment;
use App\Models\TsaldoPointTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardLiveController extends Controller
{
    public function pemesanan()
    {
        $data = DB::table('tsaldo_point_histories')
            ->orderBy('tsaldo_point_histories.created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Data transactions',
            'transaction' => $data,
        ], 200);
    }

    public function payment()
    {
        $data = TsaldoPointPayment::all();

        return response()->json([
            'success' => true,
            'message' => 'Data transactions',
            'payment' => $data,
        ], 200);
    }

    public function getLastPointByUserId($id)
    {
        $point = TsaldoPointHistories::where('user_id', $id)->orderBy('created_at', 'desc')->first();

        if (!$point) {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'data showed successfully',
            'point' => $point,
        ], 200);
    }

    public function getDetailPemesananById($id)
    {
        $point = DB::table('tsaldo_point_transaction')
            ->join('tsaldo_point_histories', 'tsaldo_point_transaction.histories_id', '=', 'tsaldo_point_histories.id')
            ->select('tsaldo_point_transaction.*', 'tsaldo_point_histories.*', 'tsaldo_point_histories.created_at as createdAt')
            ->where('histories_id', $id)
            ->first();



        if (!$point) {
            return response()->json([
                'success' => false,
                'message' => 'Data id not found',
            ], 400);
        }


        // if ($point->tsaldo_point_payment_id != '001') {
        //     $point = DB::table('tsaldo_point_transaction')
        //         ->join('tsaldo_point_histories', 'tsaldo_point_transaction.histories_id', '=', 'tsaldo_point_histories.id')
        //         ->join('tsaldo_point_payment', 'tsaldo_point_transaction.tsaldo_point_payment_id', '=', 'tsaldo_point_payment.id')
        //         ->select('tsaldo_point_transaction.*', 'tsaldo_point_histories.*', 'tsaldo_point_payment.*', 'tsaldo_point_transaction.id as histories_id', 'tsaldo_point_transaction.created_at as createdAt', 'tsaldo_point_transaction.updated_at as updatedAt', 'tsaldo_point_transaction.description as histories_description', 'tsaldo_point_transaction.id as histories_id')
        //         ->where('histories_id', $id)
        //         ->first();
        // }


        return response()->json([
            'success' => true,
            'message' => 'data showed successfully',
            'point_histories' => $point,
        ], 200);
    }
    public function getDetailPaymentById($id)
    {
        $payment = TsaldoPointPayment::find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 400);
        }


        return response()->json([
            'success' => true,
            'message' => 'data showed successfully',
            'payment' => $payment,
        ], 200);
    }

    public function getHistoriesPointByUser($id)
    {
        $point = DB::table('tsaldo_point_histories')
            // ->join('tsaldo_point_histories', 'tsaldo_point_transaction.histories_id', '=', 'tsaldo_point_histories.id')
            // ->join('tsaldo_point_payment', 'tsaldo_point_transaction.tsaldo_point_payment_id', '=', 'tsaldo_point_payment.id')
            // ->select('tsaldo_point_transaction.*', 'tsaldo_point_histories.*', 'tsaldo_point_payment.*', 'tsaldo_point_transaction.id as histories_id', 'tsaldo_point_transaction.created_at as createdAt', 'tsaldo_point_transaction.updated_at as updatedAt', 'tsaldo_point_transaction.description as histories_description', 'tsaldo_point_transaction.id as histories_id')
            ->where('tsaldo_point_histories.user_id', $id)
            ->orderBy('tsaldo_point_histories.created_at', 'desc')
            ->get();

        if (!count($point)) {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'data showed successfully',
            'point_histories' => $point,
        ], 200);
    }

    public function getLastPointByUserIdDashboard($id)
    {
        $point = DB::table('tsaldo_point_histories')
            ->where('tsaldo_point_histories.user_id', $id)
            ->orderBy('tsaldo_point_histories.created_at', 'desc')
            ->first();

        if (!$point) {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'data showed successfully',
            'point_histories' => $point,
        ], 200);
    }
}
