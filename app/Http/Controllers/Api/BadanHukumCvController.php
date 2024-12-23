<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use Xendit\Xendit;
use Carbon\Carbon;

class BadanHukumCvController extends Controller
{
    protected $serverKey;

    public function __construct()
    {
        $this->serverKey = config('xendit.xendit_development_key');
    }
    public function getDataDetailOrderById($id)
    {
        $detail = Order::find($id);


        if (!$detail) {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'data showed successfully',
            'detail' => $detail,
        ], 200);
    }
    public function inOrder(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->all(), [
            'tnos_subservice_id' => 'required',
            'file_document.*' => 'mimes:jpg,jpeg,png',
            'file_document' => 'required',
            'name_badan_hukum' => 'required',
            'modal_dasar' => 'required|numeric',
            'modal_disetor' => 'required|numeric',
            'alamat_badan_hukum' => 'required',
            'pemegang_saham' => 'required',
            'susunan_direksi' => 'required',
            'bidang_usaha' => 'required',
            'email_badan_hukum' => 'required|email',
            'phone_badan_hukum' => 'required|numeric',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages()
            ], 200);
        }
        // DB::beginTransaction();
        // try {
            $harga_dasar = 0;
            $harga_total = 0;
            $harga_user = 0;
            $pendapatan_tnos = 0;
            $pendapatan_mitra = 0;
            // var_dump($request->all());
            $harga_dasar = 3500000 ;

            $harga_total = $harga_dasar;
            $harga_user = $harga_dasar ;
            $pendapatan_tnos = $harga_total * 0.2;
            $pendapatan_mitra = $harga_total * 0.8;

            // var_dump("total harga: ".$harga_total);
            // var_dump("harga user: ".$harga_user);
            // var_dump("pendapatan tnos: ".$pendapatan_tnos);
            // var_dump("pendapatan mitra: ".$pendapatan_mitra);

            if ($request->file('file_document')) {
                foreach ($request->file('file_document') as $file) {
                    // var_dump('1');
                    $getImage = $file;
                    $imageName = rand(10000000, 99999999) . time() .  '.' . $getImage->getClientOriginalExtension();
                    $path = public_path() . '/images/cv/';
                    $imagePath = URL::to('/') . '/images/cv/' . $imageName;
                    $getImage->move($path, $imageName);

                    $image = array(
                        "image_name" => $imageName,
                        "image_url" => $imagePath,
                        "mime" => $getImage->getClientMimeType()
                    );
                    // var_dump($image);
                    $data[] = $image;
                }
            }
            $dataBadanHukum = [
                'tnos_service_id' => $request->tnos_service_id,
                'tnos_subservice_id' => $request->tnos_subservice_id,
                'user_id' => $request->user_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'file_document' => json_encode($data),
                'name_badan_hukum' => $request->name_badan_hukum,
                'modal_dasar' => $request->modal_dasar,
                'modal_disetor' => $request->modal_disetor,
                'alamat_badan_hukum' => $request->alamat_badan_hukum,
                'pemegang_saham' => $request->pemegang_saham,
                'susunan_direksi' => $request->susunan_direksi,
                'bidang_usaha' => $request->bidang_usaha,
                'email_badan_hukum' => $request->email_badan_hukum,
                'phone_badan_hukum' => $request->phone_badan_hukum,
                'order_total' => $harga_user,
                'pendapatan_mitra' => $pendapatan_mitra,
                'pendapatan_tnos' => $pendapatan_tnos,
                'status_order' => 'WAIT',
                'payment_status' => 'ORDER',
            ];
            $order = Order::create($dataBadanHukum);

            // DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'data created successfully',
                'detail' => $order,
            ], 200);
        // } catch (\Exception $e) {
        //     DB::rollBack();
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'there is something wrong (Try Catch)',
        //         'error' => $e,
        //         'request_all' => $request->all()
        //     ], 500);
        //     //throw $e; //sometime you want to rollback AND throw the excecvion
        // }
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

        // $user = User::find($order->user_id);
         
        $no =  $order->phone;
        if(substr($no,0,1) == '0'){
            $no_baru = '+62' . substr($no,1);
            $no = $no_baru;
        }else{
            $no =  $order->phone;
        }
        // var_dump($no);
        // mmbr_name
        // mmbr_email
        // mmbr_phone
        $params = [
            'external_id' => 'TNOS-BCV-' . time(),
            'amount' => $order->order_total,
            'descricvion' => $order->needs ? $order->needs : '-',
            'invoice_duration' => 3600,
            'customer' => [
                'given_names' =>  $order->name,
                'email' => $order->email,
                // 'mobile_number' => "+6281389003413",
                'mobile_number' => $no ? $no : '-',
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
                'invoice_created' => [
                    'whatsapp',
                    // 'sms',
                    // 'email',
                    // 'viber'
                ],
                'invoice_reminder' => [
                    'whatsapp',
                    // 'sms',
                    // 'email',
                    // 'viber'
                ],
                'invoice_paid' => [
                    'whatsapp',
                    // 'sms',
                    // 'email',
                    // 'viber'
                ],
                'invoice_expired' => [
                    'whatsapp',
                    // 'sms',
                    // 'email',
                    // 'viber'
                ]
            ],
            'success_redirect_url' => 'https://app.tnosworld.com/transaksi/success/' . $order->id,
            'failure_redirect_url' => 'https://app.tnosworld.com/',
            'currency' => 'IDR',
            'items' => [
                [
                    'name' => 'Konsultasi Hukum',
                    'quantity' => 1,
                    'price' => $order->order_total,
                    'category' => 'Jasa Badan Hukum CV',
                ]
            ],
            'fees' => [
                [
                    'type' => 'ADMIN',
                    'value' => 0
                ]
            ]
        ];

        // var_dump($params);  

        $createInvoice = \Xendit\Invoice::create($params);

        $order->payment_status =  'UNPAID';
        $order->invoice_id = $createInvoice['id'];
        $order->external_id = $createInvoice['external_id'];
        $order->expiry_date = Carbon::now()->addMinutes(30);
        $order->update();

        return response()->json([
            'success' => true,
            'message' => 'Success create invoice',
            'order' =>  $order,
        ], 200);
    }
}
