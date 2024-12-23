<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Point;
use App\Models\PointHistory;
use App\Models\TnosGems;
use App\Models\TnosGemsTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Xendit\Xendit;

class TnosGemsController extends Controller
{
    protected $serverKey;


    public function __construct()
    {
        // update 
        $this->serverKey = config('xendit.xendit_development_key');
    }
    public function fetchAllProduct()
    {
        $product = TnosGems::where('status', 'active')->first();

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'data showed successfully',
            'product' => $product,
        ], 200);
    }
    public function fetchDetailOrderById($id)
    {
        $order = TnosGemsTransaction::find($id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'data showed successfully',
            'order' => $order,
        ], 200);
    }
    public function fetchPointByUserId($id)
    {
        $point = Point::where('user_id', $id)->first();

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
    public function addOrder(Request $request)
    {


        //  $no_token = rand(1000, 99999999999) . $detail->id;

        $data = [
            'user_id' => $request->user_id,
            'amount' => 10000 * $request->items['quantity'],
            'description' => "Top Up",
            'customer' => json_encode($request->customer),
            'items' => json_encode($request->items),
        ];
        // var_dump($data);
        $order = TnosGemsTransaction::create($data);


        return response()->json([
            'success' => true,
            'message' => 'Order was successful',
            'order' => $order,
        ], 200);
    }

    public function inPayment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages()
            ], 200);
        }

        Xendit::setApiKey($this->serverKey);

        $order = TnosGemsTransaction::find($request->order_id);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order id not found',
            ], 500);
        }



        if ($order->payment_status != 'ORDER') {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 500);
        }

        // // $user = User::find($OrderPengamananPerorang->user_id);
        $customer = json_decode($order->customer);
        $items = json_decode($order->items);
        // var_dump($customer->mobile_number);
        $no =  $customer->mobile_number;
        if (substr($no, 0, 1) == '0') {
            $no_baru = '+62' . substr($no, 1);
            $no = $no_baru;
        } else {
            $no =  $customer->mobile_number;
        }
        // mmbr_name
        // mmbr_email
        // mmbr_phone



        $params = [
            'external_id' => 'TNOS-TP-' . time(),
            'amount' => $order->amount,
            'description' => $order->description,
            'invoice_duration' => 3600,
            'customer' => [
                'given_names' =>  $customer->gives_name,
                'email' => $customer->email,
                'mobile_number' => $no ? $no : "-",
                // 'addresses' => [
                //     [
                //         'city' => 'Jakarta Selatan',
                //         'country' => 'Indonesia',
                //         'postal_code' => '12345',
                //         'state' => 'Daerah Khusus Ibukota Jakarta',
                //         'street_line1' => 'Jalan Makan',
                //         'street_line2' => 'Kecamatan Kebayoran Baru'
                //     ]
                // ]
            ],
            'customer_notification_preference' => [
                // 'invoice_created' => [
                //     'whatsapp',
                //     // 'sms',
                //     // 'email',
                //     // 'viber'
                // ],
                // 'invoice_reminder' => [
                //     'whatsapp',
                //     // 'sms',
                //     // 'email',
                //     // 'viber'
                // ],
                'invoice_paid' => [
                    'whatsapp',
                    // 'sms',
                    // 'email',
                    // 'viber'
                ],
                // 'invoice_expired' => [
                //     'whatsapp',
                //     // 'sms',
                //     // 'email',
                //     // 'viber'
                // ]
            ],
            'success_redirect_url' => 'https://app.tnosworld.com/transaksi/success/' . $order->id,
            'failure_redirect_url' => 'https://app.tnosworld.com/',
            'currency' => 'IDR',
            'items' => [
                [
                    'name' => 'TNOS GEMS',
                    'quantity' => $items->quantity,
                    'price' =>  10000,
                    'category' => 'Top Up',
                ]
            ],
            'fees' => [
                [
                    'type' => 'ADMIN',
                    'value' => 0
                ]
            ]
        ];



        $createInvoice = \Xendit\Invoice::create($params);

        $order->payment_status = 'UNPAID';
        $order->invoice_id = $createInvoice['id'];
        $order->external_id = $createInvoice['external_id'];
        $order->expiry_date = Carbon::now()->addMinutes(60);
        $order->update();

        return response()->json([
            'success' => true,
            'message' => 'Success create invoice',
            'order' =>  $order,
            // "point" => $point,
            // "point_histories" => $pointHistory
        ], 200);
    }

    public function getHistoriesPointByUser($id)
    {
        $point = DB::table('point_histories')
            ->join('points', 'point_histories.point_id', '=', 'points.id')
            ->join('tnos_gems_transactions', 'point_histories.tnos_gems_transaction_id', '=', 'tnos_gems_transactions.id')
            ->select('point_histories.*', 'points.*', 'tnos_gems_transactions.*', 'point_histories.id as histories_id', 'point_histories.created_at as createdAt', 'point_histories.updated_at as updatedAt', 'point_histories.description as histories_description', 'point_histories.id as histories_id')
            ->where('points.user_id', $id)
            ->orderBy('point_histories.created_at', 'desc')
            ->get();

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

    public function getHistoriesPointById($id)
    {
        $point = DB::table('point_histories')
            ->join('points', 'point_histories.point_id', '=', 'points.id')
            ->join('tnos_gems_transactions', 'point_histories.tnos_gems_transaction_id', '=', 'tnos_gems_transactions.id')
            ->select('point_histories.*', 'points.*', 'tnos_gems_transactions.*', 'point_histories.id as histories_id', 'point_histories.created_at as createdAt', 'point_histories.updated_at as updatedAt', 'point_histories.description as histories_description', 'point_histories.id as histories_id')
            ->where('point_histories.id', $id)
            ->orderBy('point_histories.created_at', 'desc')
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
