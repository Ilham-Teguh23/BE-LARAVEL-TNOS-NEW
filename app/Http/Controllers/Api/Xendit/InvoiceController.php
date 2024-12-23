<?php

namespace App\Http\Controllers\Api\Xendit;

// use App\Http\Controllers\Api\MessageUserController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\Send\PaymentController;
use App\Models\Order;
use App\Models\TsaldoPointHistories;
use App\Models\TsaldoPointPayment;
use App\Models\TsaldoPointTransaction;
use Illuminate\Http\Request;
use Xendit\Xendit;
use App\Models\User;
use PhpParser\Node\Stmt\TryCatch;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;

class InvoiceController extends Controller
{
    protected $serverKey;
    protected $callback_token;

    public function __construct()
    {
        $this->serverKey = config("xendit.xendit_development_key");
        $this->callback_token = config("xendit.xendit_development_callback_token");
    }

    public function createInvoice(Request $request)
    {
        Xendit::setApiKey($this->serverKey);

        // $params = [
        //     'external_id' => 'demo_147580196270',
        //     'payer_email' => 'sample_email@xendit.co',
        //     'description' => 'Trip to Bali dwdwdawdawd',
        //     'amount' => 32000
        // ];
        $params = [
            'external_id' => 'TNOS-' . time(),
            'amount' => 100000,
            'description' => 'trip',
            'invoice_duration' => 86400,
            'customer' => [
                'given_names' => 'dicki',
                'email' => 'dicki-prasetya@tnos.world',
                'mobile_number' => '+6287774441111',
                'addresses' => [
                    [
                        'city' => 'Jakarta Selatan',
                        'country' => 'Indonesia',
                        'postal_code' => '12345',
                        'state' => 'Daerah Khusus Ibukota Jakarta',
                        'street_line1' => 'Jalan Makan',
                        'street_line2' => 'Kecamatan Kebayoran Baru'
                    ]
                ]
            ],
            // 'customer_notification_preference' => [
            //     'invoice_created' => [
            //         'whatsapp',
            //         'sms',
            //         'email',
            //         'viber'
            //     ],
            //     'invoice_reminder' => [
            //         'whatsapp',
            //         'sms',
            //         'email',
            //         'viber'
            //     ],
            //     'invoice_paid' => [
            //         'whatsapp',
            //         'sms',
            //         'email',
            //         'viber'
            //     ],
            //     'invoice_expired' => [
            //         'whatsapp',
            //         'sms',
            //         'email',
            //         'viber'
            //     ]
            // ],
            'success_redirect_url' => 'https://app.tnosworld.com/transaksi-konsultasi-hukum/success',
            'failure_redirect_url' => 'https://app.tnosworld.com/',
            'currency' => 'IDR',
            'items' => [
                [
                    'name' => 'Air Conditioner',
                    'quantity' => 1,
                    'price' => 100000,
                    'category' => 'Electronic',
                    'url' => 'https=>//yourcompany.com/example_item'
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
        return response()->json([
            'success' => true,
            'message' => 'Success create invoice',
            'createInvoice' =>  $createInvoice,
        ], 200);
    }
    public function getAllInvoice(Request $request)
    {
        Xendit::setApiKey($this->serverKey);

        $getAllInvoice = \Xendit\Invoice::retrieveAll();

        return response()->json([
            'success' => true,
            'message' => 'Success all invoice',
            'getAllInvoice' =>  $getAllInvoice,
        ], 200);
    }

    public function callback(Request $request)
    {
        try {
            // $xenditXCallbackToken = $this->callback_token;
            $reqHeaders = $request->header('x-callback-token');;
            $xIncomingCallbackTokenHeader = isset($reqHeaders) ? $reqHeaders : "";

            // $response = Http::withHeaders([
            //     'Authorization' => 'Basic ' . base64_encode($this->serverKey)
            // ])->get("https://api.xendit.co/v2/invoices/" . $request->invoice_id);

            $order = Order::where('external_id', $request->external_id)->firstOrFail();

            if ($xIncomingCallbackTokenHeader) {
                if ($request->status == 'PAID' || $request->status == 'SETTLED') {

                    $order = Order::where('invoice_id', $request->id)->first();

                    if ($order) {
                        $prefix = "B2B" . '-' . date('mY');

                        $tnos_invoice_id = IdGenerator::generate(['table' => 'b2b_orders', 'field' => 'tnos_invoice_id', 'length' => 14, 'prefix' => $prefix, 'reset_on_prefix_change' => true]);

                        $order->tnos_invoice_id = $tnos_invoice_id;
                        $order->payment_status = $request->status;
                        $order->payment_method = $request->payment_method;
                        $order->payment_channel = $request->payment_channel;
                        $order->paid_amount = $request->paid_amount;
                        $order->paid_at = $request->paid_at;
                        $order->status_order = '002';
                        $order->update();
                    } else {
                        $orderTsaldo = TsaldoPointPayment::where('invoice_id', $request->id)->first();

                        $prefix = "TSD" . '-' . date('mY');

                        $invoice = IdGenerator::generate(['table' => 'tsaldo_point_payment', 'field' => 'tsaldo_invoice_id', 'length' => 14, 'prefix' => $prefix, 'reset_on_prefix_change' => true]);

                        $orderTsaldo->tsaldo_invoice_id = $invoice;
                        $orderTsaldo->payment_status = $request->status;
                        $orderTsaldo->payment_method = $request->payment_method;
                        $orderTsaldo->payment_channel = $request->payment_channel;
                        $orderTsaldo->paid_amount = $request->paid_amount;
                        $orderTsaldo->paid_at = $request->paid_at;
                        $orderTsaldo->status_order = '010';
                        $orderTsaldo->update();

                        $point = TsaldoPointHistories::where('user_id', $orderTsaldo->user_id)->orderBy('created_at', 'desc')->first();

                        if (!$point) {
                            $data = [
                                "user_id" => $orderTsaldo->user_id,
                                "in_point" => $orderTsaldo->amount / 10000,
                                "before_point" => $orderTsaldo->amount / 10000,
                                "point" => $orderTsaldo->amount / 10000,
                                "description" => $orderTsaldo->description,
                            ];
                            $pointCreate = TsaldoPointHistories::create($data);

                            $dataHistory = [
                                "histories_id" => $pointCreate->id,
                                "tsaldo_point_payment_id" => $orderTsaldo->id,
                                "description" => $orderTsaldo->description,
                            ];

                            $pointHistory = TsaldoPointTransaction::create($dataHistory);
                        } else {

                            $data = [
                                "user_id" => $orderTsaldo->user_id,
                                "in_point" => $orderTsaldo->amount / 10000,
                                "before_point" => $point->point,
                                "point" => $point->point + $orderTsaldo->amount / 10000,
                                "description" => $orderTsaldo->description,
                            ];
                            $pointCreate = TsaldoPointHistories::create($data);

                            $dataHistory = [
                                "histories_id" => $pointCreate->id,
                                "tsaldo_point_payment_id" => $orderTsaldo->id,
                                "description" => $orderTsaldo->description,
                            ];

                            $pointHistory = TsaldoPointTransaction::create($dataHistory);
                        }
                    }

                    return response()->json([
                        'success' => false,
                        'message' => "Callback Successfully"
                    ], 200);
                } else {
                    $order = Order::where('invoice_id', $request->id)->first();
                    if ($order) {
                        $order->payment_status = $request->status;
                        $order->status_order = '011';
                        // $order->expiry_date = $request->expiry_date;
                        $order->update();
                    } else {
                        $orderTsaldo = TsaldoPointPayment::where('invoice_id', $request->id)->first();

                        $orderTsaldo->payment_status = $request->status;
                        $orderTsaldo->status_order = '011';
                        $orderTsaldo->update();
                    }

                    return response()->json([
                        'success' => false,
                        'message' => "Callback Successfully"
                    ], 200);
                }
            } else {
                    return response()->json([
                    'success' => false,
                    'message' => "Callback token is invalid"
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }

        die();

        if ($xIncomingCallbackTokenHeader == $xenditXCallbackToken) {

            if ($response->json()[0]["status"] == 'PAID' || $response->json()[0]['status'] == 'SETTLED') {


                // if ($order) {
                // } else {
                // $orderTsaldo = TsaldoPointPayment::where('invoice_id', $request->id)->first();

                // $prefix = "TSD" . '-' . date('mY');

                // $invoice = IdGenerator::generate(['table' => 'tsaldo_point_payment', 'field' => 'tsaldo_invoice_id', 'length' => 14, 'prefix' => $prefix, 'reset_on_prefix_change' => true]);

                // $orderTsaldo->tsaldo_invoice_id = $invoice;
                // $orderTsaldo->payment_status = $request->status;
                // $orderTsaldo->payment_method = $request->payment_method;
                // $orderTsaldo->payment_channel = $request->payment_channel;
                // $orderTsaldo->paid_amount = $request->paid_amount;
                // $orderTsaldo->paid_at = $request->paid_at;
                // $orderTsaldo->status_order = '010';
                // $orderTsaldo->update();

                // $point = TsaldoPointHistories::where('user_id', $orderTsaldo->user_id)->orderBy('created_at', 'desc')->first();

                // if (!$point) {
                //     $data = [
                //         "user_id" => $orderTsaldo->user_id,
                //         "in_point" => $orderTsaldo->amount / 10000,
                //         "before_point" => $orderTsaldo->amount / 10000,
                //         "point" => $orderTsaldo->amount / 10000,
                //         "description" => $orderTsaldo->description,
                //     ];
                //     $pointCreate = TsaldoPointHistories::create($data);

                //     $dataHistory = [
                //         "histories_id" => $pointCreate->id,
                //         "tsaldo_point_payment_id" => $orderTsaldo->id,
                //         "description" => $orderTsaldo->description,
                //     ];

                //     $pointHistory = TsaldoPointTransaction::create($dataHistory);
                // } else {

                //     $data = [
                //         "user_id" => $orderTsaldo->user_id,
                //         "in_point" => $orderTsaldo->amount / 10000,
                //         "before_point" => $point->point,
                //         "point" => $point->point + $orderTsaldo->amount / 10000,
                //         "description" => $orderTsaldo->description,
                //     ];
                //     $pointCreate = TsaldoPointHistories::create($data);

                //     $dataHistory = [
                //         "histories_id" => $pointCreate->id,
                //         "tsaldo_point_payment_id" => $orderTsaldo->id,
                //         "description" => $orderTsaldo->description,
                //     ];

                //     $pointHistory = TsaldoPointTransaction::create($dataHistory);
                // }
                // }

                // $dataMessage = [
                //     'title' => 'Pembayaran Berhasil',
                //     'description' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Aspernatur reiciendis itaque in id vel rerum.',
                // ];

                $email = new PaymentController();
                $email->paymentSuccessEmail($order);
                // $message = new MessageUserController;
                // $message->generateMessage($order->user_id,$order->id,$dataMessage , $order->status_order);
                return response()->json([
                    'success' => false,
                    'message' => "Callback Successfully"
                ], 200);
            } else {
                $order = Order::where('invoice_id', $request->id)->first();
                if ($order) {
                    $order->payment_status = $request->status;
                    $order->status_order = '011';
                    // $order->expiry_date = $request->expiry_date;
                    $order->update();
                } else {
                    $orderTsaldo = TsaldoPointPayment::where('invoice_id', $request->id)->first();

                    $orderTsaldo->payment_status = $request->status;
                    $orderTsaldo->status_order = '011';
                    $orderTsaldo->update();
                }

                return response()->json([
                    'success' => false,
                    'message' => "Callback Successfully"
                ], 200);
            }
        } else {
            // $order = Order::where('invoice_id', $request->id)->first();
            // $order->payment_status = 'Expired';
            // $order->expiry_date = $request->expiry_date;
            // $order->update();
            return response()->json([
                'success' => false,
                'message' => "Callback token is invalid"
            ], 404);
        }
    }

    public function user($id)
    {
        $user =  Order::where('id', $id)->first();
        return response()->json([
            'success' => true,
            'user' => $user,
        ]);
    }
}
