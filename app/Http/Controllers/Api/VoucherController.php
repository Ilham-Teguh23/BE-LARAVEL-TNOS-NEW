<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TsaldoPointHistories;
use App\Models\TsaldoPointPayment;
use App\Models\TsaldoPointTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Xendit\Xendit;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class VoucherController extends Controller
{
    protected $serverKey;


    public function __construct()
    {
        // update
        $this->serverKey = config('xendit.xendit_production_key');
    }
   public function inVoucher(Request $request)
   {
    Xendit::setApiKey($this->serverKey);



    $data = [
        'user_id' => $request->user_id,
        // 'amount' => 10000,
        'amount' => 400000,
        'description' => "Pembelian Voucher",
        'customer' => json_encode($request->customer),
        'items' => json_encode([
            "id" => '1',
            "quantity" => 1,
        ]),
    ];


    $order = TsaldoPointPayment::create($data);


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
        'description' => 'Pembelian Voucher',
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
    ], 200);
   }

   public function outVoucher(Request $request)
   {

    //$point = TsaldoPointHistories::where('user_id', $request->user_id)->orderBy('created_at', 'desc')->first();
	$point = DB::select( DB::raw("SELECT id,point FROM tsaldo_point_histories WHERE user_id = :id ORDER BY created_at DESC LIMIT 1"), array(
	   'id' => $request->user_id,
	 ));

    if (!$point) {
        return response()->json([
            'success' => false,
            'message' => 'Data Tidak ditemukan',
        ], 200);
     }

    $data = [
        "user_id" => $request->user_id,
        "out_point" => $request->point,
        "before_point" => $point[0]->point,
        "point" => $point[0]->point - $request->point,
        "description" => $request->description,
    ];

    $pointCreate = TsaldoPointHistories::create($data);

    // $prefix = "TSD" . '-' . date('mY');

    // $invoice = IdGenerator::generate(['table' => 'tsaldo_point_payment', 'field' => 'tsaldo_invoice_id', 'length' => 14, 'prefix' => $prefix, 'reset_on_prefix_change' => true]);


    // $data = [
    //     'user_id' => $request->user_id,
    //     'amount' => $pointCreate->out_point * 10000,
    //     'description' => "Penggunaan Voucher",
    //     'customer' => json_encode($request->customer),
    //     'items' => json_encode([
    //         "id" => '1',
    //         "quantity" => 1,
    //     ]),
    //     'tsaldo_invoice_id' => $invoice,
    //     'external_id' => 'TNOS-TP-' . time(),
    //     'payment_status' => "PAID",
    //     'payment_method' => "TNOS POIN",
    //     'payment_channel' => "POIN",
    //     'paid_amount' => $pointCreate->out_point * 10000,
    //     'paid_at' => Carbon::now(),
    //     'status_order' => "010",
    // ];

    // $order = TsaldoPointPayment::create($data);

    $dataHistory = [
        "histories_id" => $pointCreate->id,
        "tsaldo_point_payment_id" => '001',
        "description" => $request->description,
    ];

    $pointHistory = TsaldoPointTransaction::create($dataHistory);

    return response()->json([
        'success' => true,
        'message' => 'Success out voucher',
        'history' =>  $pointHistory,
        'point' =>  $pointCreate,
    ], 200);

   }

   public function checkSaldo($id)
   {
    //$payment = TsaldoPointPayment::where('user_id',$id)->orderBy('created_at','desc')->first();
	$payment = DB::select( DB::raw("SELECT id,point FROM tsaldo_point_histories WHERE user_id = :id ORDER BY created_at DESC LIMIT 1"), array(
	   'id' => $id,
	 ));

    if (!$payment) {
        return response()->json([
            'success' => false,
            'message' => 'Data Tidak ditemukan',
        ], 200);
     }

     if($payment[0]->point != 40){
        return response()->json([
            'success' => false,
            'message' => 'Data point tidak sama dengan 400, ini bukan voucher',
        ], 200);
     }

    return response()->json([
        'success' => true,
        'message' => 'Success out voucher',
        'payment' =>  $payment[0],

    ], 200);
   }
}
