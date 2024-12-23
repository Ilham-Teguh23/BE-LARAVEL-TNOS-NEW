<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HargaKomponen;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Xendit\Xendit;
use Carbon\Carbon;
use Exception;
use Xendit\Configuration;
use Xendit\Invoice;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\ValidationException;
use Throwable;
use Xendit\Invoice\InvoiceApi;
use Illuminate\Support\Str;

class BadanHukumController extends Controller
{
    protected $serverKey, $callback_token, $hargaKomponen;
    var $apiInstance = null;

    public function __construct()
    {
        // $this->serverKey = config("xendit.xendit_development_key");
        // $this->callback_token = config("xendit.xendit_development_callback_token");

        $this->serverKey = config("xendit.xendit_production_key");
        $this->callback_token = config("xendit.xendit_production_callback_token");
        $this->hargaKomponen = new HargaKomponen();
    }

    public function getDataDetailOrderById($id)
    {
        $detail = Order::with(['partnerdeka'])
            ->where('id', $id)
            ->first();

        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 400);
        }

        if ($detail["tnos_service_id"] === "6" && $detail["tnos_subservice_id"] === "1") {

            return response()->json([
                'success' => true,
                'message' => 'data showed successfully',
                'type' => 'p1',
                'detail' => [
                    "detail" => $detail,
                    "layanan" => $detail->history_pwan->layanan,
                    "provider" => $detail->history_pwan->layanan->providers
                ]
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'data showed successfully',
                'type' => 'others',
                'detail' => $detail,
            ], 200);
        }
    }

    public function inOrder(Request $request)
    {
        $datas = $request->all();
        $type = $request->type;

        if (!empty($type === "deka")) {
            $order = Order::inorder_deka_badan_hukum($request, $type, $datas);
        } else {
            $order = Order::in_order_badan_hukum($request, $type, $datas);
        }

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'data created successfully',
            'detail' => $order,
        ], 200);
    }

    public function tracking_order_deka(Request $request)
    {
        $nomor_po = Order::where('code', $request->nomor_po)->first();

        if (!empty($nomor_po)) {

            $nomor_po->update([
                'tracking_status'  => $request->status,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'data created successfully',
                'detail' => $nomor_po,
            ], 200);
        } else {
            return response()->json([
                'success' => true,
                'message' => 'data nomor po tidak ada',
                'detail' => 'nomor po tidak ada',
            ], 200);
        }
    }

    public function update_status_tracking($id, Request $request)
    {
        $update_status_tracking = Order::update_status_tracking($id, $request);

        return response()->json([
            'success' => true,
            'message' => 'data created successfully',
            'data' => $update_status_tracking,
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

        // Configuration::setXenditKey($this->serverKey);
        // Xendit::setApiKey($this->serverKey);

        $order = Order::find($request->order_id);

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


        $no =  $order->phone;
        if (substr($no, 0, 1) == '0') {
            $no_baru = '+62' . substr($no, 1);
            $no = $no_baru;
        } else {
            $no =  $order->phone;
        }

        $layanan = "";
        if ($order->tnos_service_id === "2") {
            if ($order->tnos_subservice_id === "2") {
                $kode = "PK";
                $layanan = "Pengamanan Korporat";
                $description = "-";
            } else {
                $kode = "";
            }
        } elseif ($order->tnos_service_id === "3") {
            if ($order->tnos_subservice_id === "1") {
                $layanan = "Badan Usaha PT";
            } elseif ($order->tnos_subservice_id === "2") {
                $layanan = "Badan Usaha CV";
            } elseif ($order->tnos_subservice_id === "3") {
                $layanan = "Badan Hukum Yayasan";
            } elseif ($order->tnos_subservice_id === "4") {
                $layanan = "Badan Hukum Perkumpulan";
            } elseif ($order->tnos_subservice_id === "5") {
                $layanan = "Badan Hukum Asosiasi";
            } elseif ($order->tnos_subservice_id === "6") {
                $layanan = "Legalitas Lainnya";
            } elseif ($order->tnos_subservice_id === "7") {
                $layanan = "Solusi Hukum";
            } else if ($order->tnos_subservice_id === "8") {
                $layanan = "Pembayaran Lainnya";
            } else {
                $layanan = "-";
            }
        } elseif ($order->tnos_service_id === "4") {
            if ($order->tnos_subservice_id === "1") {
                $layanan = "PAS";
            }
        } elseif ($order->tnos_service_id === "5") {
            if ($order->tnos_subservice_id === "1") {
                $layanan = "TRIGER";
            }
        } else {
            $layanan = "Error";
        }
        // $params = [
        //     'external_id' => 'TNOS-' . $kode . '-' . time(),
        //     'amount' => $order->order_total,
        //     'description' => $description ? $description : '-',
        //     'invoice_duration' => 3600,
        //     'customer' => [
        //         'given_names' =>  $order->name,
        //         'email' => $order->email,
        //         'mobile_number' => $no ? $no : '-',
        //     ],
        //     'customer_notification_preference' => [
        //         // 'invoice_created' => [
        //         //     'whatsapp',
        //         //     // 'sms',
        //         //     // 'email',
        //         //     // 'viber'
        //         // ],
        //         // 'invoice_reminder' => [
        //         //     'whatsapp',
        //         //     // 'sms',
        //         //     // 'email',
        //         //     // 'viber'
        //         // ],
        //         'invoice_paid' => [
        //             'whatsapp',
        //             // 'sms',
        //             // 'email',
        //             // 'viber'
        //         ],
        //         'invoice_expired' => [
        //             'whatsapp',
        //             // 'sms',
        //             // 'email',
        //             // 'viber'
        //         ]
        //     ],
        //     'success_redirect_url' => 'https://app.tnosworld.com/payment/success/' . $order->id,
        //     'failure_redirect_url' => 'https://app.tnosworld.com/payment/failure/' . $order->id,
        //     'currency' => 'IDR',
        //     'items' => [
        //         [
        //             'name' => $layanan,
        //             'quantity' => 1,
        //             'price' => $order->order_total,
        //             'category' => 'Jasa Pembuatan Badan Usaha atau Hukum',
        //         ]
        //     ],
        //     'fees' => [
        //         [
        //             'type' => 'ADMIN',
        //             'value' => 0
        //         ]
        //     ]
        // ];

        Xendit::setApiKey($this->serverKey);
        $params = [
            "external_id" => $order->external_id,
            "amount" => $order->order_total,
            // "amount" => 10000,
            'description' => $order->needs,
            'invoice_duration' => 7200,
            'customer' => [
                'given_names' =>  $order->name ? $order->name : '-',
                'email' => $order->email ? $order->email : 'test@gmail.com',
                'mobile_number' => $no ? $no : '-',
            ],
            'customer_notification_preference' => [
                'invoice_paid' => [
                    'whatsapp',
                ],
                'invoice_expired' => [
                    'whatsapp',
                ]
            ],
            'success_redirect_url' => 'https://app.tnosworld.com/payment/custom/success/' . $order->id,
            'failure_redirect_url' => 'https://app.tnosworld.com/payment/failure/' . $order->id,
            'currency' => 'IDR',
            'items' => [
                [
                    'name' => $layanan,
                    'quantity' => 1,
                    'price' => $order->order_total,
                    // 'price' => 10000,
                    'category' => 'Jasa Pembuatan Badan Usaha atau Hukum',
                ]
            ],
            'fees' => [
                [
                    'type' => 'ADMIN',
                    'value' => 0
                ]
            ]
        ];

        $createInvoiceRequest = \Xendit\Invoice::create($params);

        // $result = $this->apiInstance->createInvoice($createInvoiceRequest);

        $order->payment_status =  'UNPAID';
        $order->invoice_id = $createInvoiceRequest['id'];
        $order->external_id = $createInvoiceRequest['external_id'];
        $order->expiry_date = Carbon::now()->addMinutes(120);
        $order->update();

        return response()->json([
            'success' => true,
            'message' => 'Success create invoice',
            'order' =>  $order
        ], 200);
    }
}
