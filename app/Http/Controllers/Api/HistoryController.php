<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class HistoryController extends Controller
{
    public function getDataHistory($id, Request $request)
    {
        $query = DB::table('b2b_orders')
            ->select(
                "b2b_orders.*",
                "b2b_orders.created_at as day",
            )
            ->where('b2b_orders.user_id', $id);

        if ($request->type == "deka") {
            $query->where("type", "deka");
        }

        $data = $query->orderBy('b2b_orders.created_at', 'DESC')
            ->get();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'data showed successfully',
            'detail' => $data,
        ], 200);
    }

    public function getHistoryData($id)
    {
        $data = DB::table('b2b_orders')
            ->select(
                "b2b_orders.*",
                "b2b_orders.created_at as day",
            )
            ->where('b2b_orders.user_id', $id)
            ->orderBy('b2b_orders.created_at', 'DESC')
            ->get();

        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'data showed successfully',
            'detail' => $data,
        ], 200);
    }

    public function getOrderById($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'data not found',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'data showed successfully',
            'detail' => $order,
        ], 200);
    }

    public function getAllOrder(Request $request)
    {
        $order = Order::with(["partnerdeka"])->orderBy("created_at", "DESC");

        if ($request->has("type")) {
            if ($request->type == "transaksi") {
            } else if ($request->type == "pemesanan") {
                $order->where("status_order", "RUN")
                    ->where("payment_status", "SETTLED")
                    ->orWhere("payment_status", "PAID");
            } else if ($request->type == "pendapatan") {
                $order->where("paid_at", "!=", NULL);
            } else if ($request->type == "pembayaran") {
                $order->where("payment_status", "SETTLED");
            }
        }

        $order = $order->get();


        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'data not found',
            ], 400);
        }

        $order->transform(function($item, $key) {
            $item->partner_name = $item->partnerdeka->name ?? null;
            unset($item->partnerdeka);
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'all data showed successfully',
            'detail' => $order,
        ], 200);
    }
}
