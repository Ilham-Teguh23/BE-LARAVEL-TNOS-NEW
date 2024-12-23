<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Xendit\Xendit;

class OrderConsultationController extends Controller
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
            'user_id' => 'required',
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'time' => 'required',
            'needs' => 'required',
            'duration' => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages()
            ], 200);
        }
        DB::beginTransaction();
        try {
            $date_now = Carbon::parse($request->time);
            $format_time_now = $date_now->format('H:i:s');
            $date_shift_end = Carbon::now();
            $normal_shift_start = Carbon::now()->format('06:00:00');
            $normal_shift_end = Carbon::now()->format('21:59:59');
            $night_shift_start_s = Carbon::now()->format('22:00:00');
            $night_shift_end_s = $date_shift_end->format('23:59:59');
            $night_shift_start_p = $date_shift_end->format('00:00:00');
            $night_shift_end_p = $date_shift_end->format('05:59:59');

            // var_dump('ini tanggal yang pilih: '.$date_now->format('Y-m-d'));


            // variable data
            $waktu_kerja = '';
            $harga_dasar = 0;
            $harga_total = 0;
            $harga_user = 0;
            $pendapatan_tnos = 0;
            $pendapatan_mitra = 0;

            //check tanggal libur
            $tanggal_libur = [
                '2023-01-01', // 1 Januari: Tahun Baru 2023 Masehi
                '2023-01-22', // 22 Januari: Tahun Baru Imlek 2574 Kongzili
                '2023-01-23', // 23 Januari: Tahun Baru Imlek 2574 Kongzili
                '2023-02-18', // 18 Februari: Isra Mikraj Nabi Muhammad SAW
                '2023-03-22', // 22 Maret: Hari Suci Nyepi Tahun Baru Saka 1945
                '2023-03-23', // 23 Maret: Hari Suci Nyepi Tahun Baru Saka 1945
                '2023-04-07', // 7 April: Wafat Isa Al Masih
                '2023-04-21', // 21, 24, 25, dan 26 April: Hari Raya Idulfitri 1444 Hijriah
                '2023-04-22', // 22-23 April: Hari Raya Idulfitri 1444 Hijriah
                '2023-04-23', // 22-23 April: Hari Raya Idulfitri 1444 Hijriah
                '2023-04-24', // 21, 24, 25, dan 26 April: Hari Raya Idulfitri 1444 Hijriah
                '2023-04-25', // 21, 24, 25, dan 26 April: Hari Raya Idulfitri 1444 Hijriah
                '2023-04-26', // 21, 24, 25, dan 26 April: Hari Raya Idulfitri 1444 Hijriah
                '2023-05-01', // 1 Mei: Hari Buruh Internasional
                '2023-05-18', // 18 Mei: Kenaikan Isa Al Masih
                '2023-06-01', // 1 Juni: Hari Lahir Pancasila
                '2023-06-02', // 2 Juni: Hari Raya Waisak
                '2023-06-04', // 4 Juni: Hari Raya Waisak 2567 BE
                '2023-06-29', // 29 Juni: Hari Raya Iduladha 1444 Hijriah
                '2023-07-19', // 19 Juli: Tahun Baru Islam 1445 Hijriah
                '2023-08-17', // 17 Agustus: Hari Kemerdekaan Republik Indonesia
                '2023-09-28', // 28 September: Maulid Nabi Muhammad SAW
                '2023-12-25', // 25 Desember: Hari Raya Natal
                '2023-12-26', // 26 Desember: Hari Raya Natal
            ];

            if(in_array($date_now->format('Y-m-d'),$tanggal_libur)){
                // var_dump('ini hari libur nasional');
                if(Carbon::parse($format_time_now)->between($normal_shift_start,$normal_shift_end)){
                    // var_dump('normal shift');
                    $waktu_kerja = '1';
                    if ($request->duration <= 20) {
                        // var_dump('kurang dari 20 menit');
                        $harga_dasar = 16250;
                        $harga_total =  $harga_dasar *  $request->duration;
                        } else {
                        // var_dump('lebih dari 20 menit');
                        $harga_dasar = 18750 ;
                        $harga_total = 325000 + ($harga_dasar * ($request->duration - 20));
                    }
                }
                elseif(Carbon::parse($format_time_now)->between($night_shift_start_s,$night_shift_end_s) || Carbon::parse($format_time_now)->between($night_shift_start_p,$night_shift_end_p)){
                    // var_dump('night shift');
                    $waktu_kerja = '2';
                    if ($request->duration <= 20) {
                        // var_dump('kurang dari 20 menit');
                        $harga_dasar = 18750;
                        $harga_total =  $harga_dasar *  $request->duration;
                        } else {
                        // var_dump('lebih dari 20 menit');
                        $harga_dasar = 20000;
                        $harga_total = 375000 + ($harga_dasar * ($request->duration - 20));
                    }
                }else{
                    // var_dump('ada yang error');
                    return response()->json([
                        'success' => false,
                        'message' => 'ada yang error pada jadwal weekend',
                    ], 500);
                }
            }else{
                if($date_now->isWeekend()){
                    // var_dump('ini hari libur saptu dan minggu');
                    if(Carbon::parse($format_time_now)->between($normal_shift_start,$normal_shift_end)){
                        // var_dump('normal shift');
                        $waktu_kerja = '1';
                        if ($request->duration <= 20) {
                            // var_dump('kurang dari 20 menit');
                            $harga_dasar = 15000;
                            $harga_total =  $harga_dasar *  $request->duration;
                            } else {
                            // var_dump('lebih dari 20 menit');
                            $harga_dasar = 17500;
                            $harga_total = 300000 + ($harga_dasar * ($request->duration - 20));
                        }
                    }
                    elseif(Carbon::parse($format_time_now)->between($night_shift_start_s,$night_shift_end_s) || Carbon::parse($format_time_now)->between($night_shift_start_p,$night_shift_end_p)){
                        // var_dump('night shift');
                        $waktu_kerja = '2';
                        if ($request->duration <= 20) {
                            // var_dump('kurang dari 20 menit');
                            $harga_dasar = 16250;
                            $harga_total =  $harga_dasar *  $request->duration;
                            } else {
                            // var_dump('lebih dari 20 menit');
                            $harga_dasar = 18750;
                            $harga_total = 325000 + ($harga_dasar * ($request->duration - 20));
                        }
                    }else{
                        // var_dump('ada yang error');
                        return response()->json([
                            'success' => false,
                            'message' => 'ada yang error pada jadwal weekend',
                        ], 500);
                    }
                }else{
                    // var_dump('ini hari biasa');
                    if(Carbon::parse($format_time_now)->between($normal_shift_start,$normal_shift_end)){
                        // var_dump('normal shift');
                        $waktu_kerja = '1';
                        if ($request->duration <= 20) {
                            // var_dump('kurang dari 20 menit');
                            $harga_dasar = 12500;
                            $harga_total =  $harga_dasar *  $request->duration;
                            } else {
                            // var_dump('lebih dari 20 menit');
                            $harga_dasar = 15000;
                            $harga_total = 250000 + ($harga_dasar * ($request->duration - 20));
                        }
                    }
                    elseif(Carbon::parse($format_time_now)->between($night_shift_start_s,$night_shift_end_s) || Carbon::parse($format_time_now)->between($night_shift_start_p,$night_shift_end_p)){
                        // var_dump('night shift');
                        $waktu_kerja = '2';
                        if ($request->duration <= 20) {
                            // var_dump('kurang dari 20 menit');
                            $harga_dasar = 15000;
                            $harga_total =  $harga_dasar *  $request->duration;
                            } else {
                            // var_dump('lebih dari 20 menit');
                            $harga_dasar = 17500;
                            $harga_total = 300000 + ($harga_dasar * ($request->duration - 20));
                        }
                    }else{
                        // var_dump('ada yang error');
                        return response()->json([
                            'success' => false,
                            'message' => 'ada yang error pada jadwal weekday',
                        ], 500);
                    }
                }
            }

            $harga_total = $harga_total;
            $harga_user = $harga_total ;
            $pendapatan_tnos = $harga_total * 0.2;
            $pendapatan_mitra = $harga_total * 0.8;
            // var_dump("total harga: ".$harga_total);
            // var_dump("harga user: ".$harga_user);
            // var_dump("pendapatan tnos: ".$pendapatan_tnos);
            // var_dump("pendapatan mitra: ".$pendapatan_mitra);


            $dataOrder = [
                'tnos_service_id' => $request->tnos_service_id,
                'tnos_subservice_id' => $request->tnos_subservice_id,
                'tnos_service_id' => $request->tnos_service_id,
                'tnos_subservice_id' => $request->tnos_subservice_id,
                'user_id' => $request->user_id,
                'needs' => $request->needs,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'time' => $request->time,
                'duration' => $request->duration,
                'waktu_kerja' => $waktu_kerja,
                'order_total' => $harga_user,
                'pendapatan_mitra' => $pendapatan_mitra,
                'pendapatan_tnos' => $pendapatan_tnos,
            ];

            // var_dump($dataOrder);

            $Order = Order::create($dataOrder);

            $orderDetail = [
                'order_id' => $Order->id
            ];

            OrderDetail::create($orderDetail);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'data created successfully',
                'detail' => $Order,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong (Try Catch)',
                'error' => $e,
                'request_all' => $request->all()
            ], 500);
            //throw $e; //sometime you want to rollback AND throw the exception
        }
    }

    public function inPayment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);
        // var_dump($request->order_id);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages()
            ], 200);
        }


        Xendit::setApiKey($this->serverKey);

        $Order = Order::find($request->order_id);


        if (!$Order) {
            return response()->json([
                'success' => false,
                'message' => 'Order id not found',
            ], 500);
        }
        if ($Order->payment_status != '0') {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 500);
        }

        $user = User::find($Order->user_id);


        $params = [
            'external_id' => 'TNOS-KH-' . time(),
            'amount' => $Order->order_total,
            'description' => $Order->needs,
            'invoice_duration' => 86400,
            'customer' => [
                'given_names' =>  $user->name,
                'email' => $user->email,
                'mobile_number' => $user->phone ? $user->phone : "-",
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
            'success_redirect_url' => 'https://app.tnosworld.com/transaksi/success/' . $Order->id,
            'failure_redirect_url' => 'https://app.tnosworld.com/',
            'currency' => 'IDR',
            'items' => [
                [
                    'name' => 'Konsultasi Hukum',
                    'quantity' => 1,
                    'price' => $Order->order_total,
                    'category' => 'Jasa Konsultasi',
                    // 'url' => 'https=>//yourcompany.com/example_item'
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

        $Order->payment_status = '1';
        $Order->invoice_id = $createInvoice['id'];
        $Order->external_id = $createInvoice['external_id'];
        $Order->update();

        return response()->json([
            'success' => true,
            'message' => 'Success create invoice',
            'order' =>  $Order,
        ], 200);
    }



}
