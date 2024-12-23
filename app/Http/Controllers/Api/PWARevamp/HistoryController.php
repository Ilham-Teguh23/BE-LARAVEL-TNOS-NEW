<?php

namespace App\Http\Controllers\Api\PWARevamp;

use App\Http\Controllers\Controller;
use App\Models\HargaKomponen;
use App\Models\History;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Xendit\Xendit;

class HistoryController extends Controller
{
    protected $orders, $history, $hargaKomponen, $serverKey, $callback_token;
    var $apiInstance = null;

    public function __construct()
    {
        $this->orders = new Order();
        $this->history = new History();
        $this->hargaKomponen = new HargaKomponen();
        $this->serverKey = config("xendit.xendit_production_key");
        $this->callback_token = config("xendit.xendit_production_callback_token");

        // $this->serverKey = config("xendit.xendit_development_key");
        // $this->callback_token = config("xendit.xendit_development_callback_token");
    }

    public function index($id)
    {
        try {

            DB::beginTransaction();

            $order = $this->orders->where("id", $id)->first();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Data Showed Successfully",
                "data" => [
                    "detail" => $order,
                    "history" => $order->history_pwan,
                    "layanan" => $order->history_pwan->layanan->name,
                    "durasi" => $order->history_pwan->layanan->durasi,
                    "provider" => $order->history_pwan->layanan->providers->name_sc,
                    "image" => $order->history_pwan->layanan->providers->image
                ]
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

            DB::beginTransaction();

            $develop = false;
            if ($develop) {
                $outputDevelop = "TESTING-";
            } else {
                $outputDevelop = "";
            }

            $date_ym = date('ym');
            $date_between = [date('Y-m-01 00:00:00'), date('Y-m-t 23:59:59')];

            $dataOrders = Order::select('code')
                ->where('type', 'tnos')
                ->whereBetween('created_at', $date_between)
                ->orderBy('code', 'desc')
                ->first();

            if (is_null($dataOrders)) {
                $nowcode = '00001';
            } else {
                $lastcode = $dataOrders->code;
                $lastcode1 = intval(substr($lastcode, -5)) + 1;
                $nowcode = str_pad($lastcode1, 5, '0', STR_PAD_LEFT);
            }

            $resultsTM = [];

            foreach ($request->json_others_component as $json_c) {
                $hargaKomponenData = $this->hargaKomponen->where("komponen_lainnya_id", $json_c["id"])
                    ->where("status", "1")
                    ->where("harga_dari", "<=", $request->orderTotal)
                    ->where("harga_sampai", ">=", $request->orderTotal)
                    ->get();

                if (!$hargaKomponenData->isEmpty()) {
                    foreach ($hargaKomponenData as $komponen) {
                        $resultsTM[] = [
                            "komponen_lainnya_id" => $json_c["id"],
                            "id" => $komponen->id,
                            "nama_komponen" => $json_c["komponen"],
                            "harga_dari" => $komponen->harga_dari,
                            "harga_sampai" => $komponen->harga_sampai,
                            "harga_akhir" => $komponen->harga_akhir,
                            "unit" => $json_c["unit"]
                        ];
                    }
                } else {
                    $results[] = [
                        "komponen_lainnya_id" => $json_c["id"],
                        "message" => "Tidak ada data yang cocok"
                    ];
                }
            }

            $totalTM = 0;

            foreach ($resultsTM as $TM) {
                $totalTM += $TM['harga_akhir'];
            }

            $order = $this->orders->create([
                "tnos_service_id" => "6",
                "tnos_subservice_id" => "1",
                "external_id" => $outputDevelop . 'TNOS-P1-FORCE-' . time(),
                "user_id" => $request->user_id,
                "type" => "tnos",
                "needs" => $request->needs,
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
                "location" => $request->location,
                "order_total" => $request->orderTotal + $totalTM,
                "pendapatan_tnos" => 10000,
                "pendapatan_mitra" => 10000,
                "tanggal_mulai" => $request->tanggal_mulai,
                "jam_mulai" => $request->jam_mulai,
                "code" => 'No.2023/DLINV/' . $date_ym . $nowcode,
                "tracking_status"   => 'Belum Bayar',
                "status_order" => "001",
                "payment_status" => "ORDER",
                "updated_at" => date("Y-m-d H:i:s"),
                "nama_pic" => $request->nama_pic,
                "nomor_pic" => $request->nomor_pic,
                "durasi_pengamanan" => $request->durasi_pengamanan
            ]);

            $history = $this->history->create([
                "b2b_orders_id" => $order->id,
                "hari" => $request->hari,
                "tanggal" => date("Y-m-d"),
                "technical_meeting" => $request->technical_meeting === 1 ? "true" : "false",
                "json_data" => json_encode($request->json),
                "id_layanan" => $request->id_layanan,
                "json_others_component" => json_encode($resultsTM),
                "status" => "1"
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Create Data Successfully",
                "data" => [
                    "order" => $order,
                    "history" => $history
                ]
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function payment(Request $request, $id, $harga)
    {
        try {

            DB::beginTransaction();

            $order = Order::find($id);

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

            Xendit::setApiKey($this->serverKey);
            $params = [
                "external_id" => $order->external_id,
                "amount" => $harga,
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
                        'name' => "P1 Force",
                        'quantity' => 1,
                        'price' => $harga,
                        'category' => 'P1 Force',
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

            // dd($createInvoiceRequest);
            $order->payment_status =  'UNPAID';
            $order->invoice_id = $createInvoiceRequest['id'];
            $order->external_id = $createInvoiceRequest['external_id'];
            $order->expiry_date = Carbon::now()->addMinutes(120);
            $order->update();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Payment Success",
                "data" => $order
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function detailTransaksi($id)
    {
        try {

            DB::beginTransaction();

            $data["orders"] = $this->orders->where("id", $id)->first();
            $data["history"] = $this->history->where("b2b_orders_id", $data["orders"]["id"])->with("layanan")->first();

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
}
