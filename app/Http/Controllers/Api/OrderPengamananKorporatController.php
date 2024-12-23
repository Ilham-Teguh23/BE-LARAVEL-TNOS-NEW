<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\OrderPengamananKorporat;
use App\Models\User;
use App\Notifications\LaravelTelegramNotification;
use Carbon\Carbon;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Polyfill\Intl\Idn\Idn;
use Xendit\Xendit;
use \KMLaravel\GeographicalCalculator\Facade\GeoFacade;


class OrderPengamananKorporatController extends Controller
{
    protected $serverKey;


    public function __construct()
    {
        // update
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

        DB::beginTransaction();
        try {

            // rumus pengamanan korporat
            $harga_perjam = 0;
            $harga_total = 0;
            $harga_user = 0;
            $pendapatan_tnos = 0;
            $pendapatan_mitra = 0;
            $biayaMakan = 0;

            if ($request->duration == 3) {
                $harga_perjam = 350000;
            } elseif ($request->duration == 4) {
                $harga_perjam = 400000;
            } elseif ($request->duration == 5) {
                $harga_perjam = 450000;
            } elseif ($request->duration == 6) {
                $harga_perjam = 500000;
            } elseif ($request->duration == 7) {
                $harga_perjam = 600000;
            } elseif ($request->duration == 8) {
                $harga_perjam = 650000;
            } elseif ($request->duration == 9) {
                $harga_perjam = 700000;
            } elseif ($request->duration == 10) {
                $harga_perjam = 750000;
            } elseif ($request->duration == 11) {
                $harga_perjam = 800000;
            } elseif ($request->duration == 12) {
                $harga_perjam = 900000;
            } else {
                $harga_perjam = 0;
            }

            if ($request->duration <= 7) {
                $biayaMakan += 100000 * $request->jml_personil;
            }elseif ($request->duration <= 4) {
                $biayaMakan += 50000 * $request->jml_personil;
            }


            $startAddress = "Jl. Arteri Pd. Indah, Kby. Lama Utara, Kec. Kby. Lama, Kota Jakarta Selatan, Daerah Khusus Ibukota Jakarta 12240";
            $startLat = "-6.2441739";
            $startLang = "106.7809541";


            $biayaMeeting = "500000";
            $harga_total =  $request->jml_personil * $harga_perjam;
            $pendapatan_tnos = $harga_user * 0.2;
            $pendapatan_mitra = $harga_user * 0.8;
            $location = $request->location;



            $generateEndLatLang = Order::generate_lat_lang($location);

            $endLat = $generateEndLatLang->lat;
            $endLng = $generateEndLatLang->lng;

            $generateDistance = Order::generate_distance($endLat,$endLng,$startLat,$startLang);

            $biayaTransport = 0;
            if ($generateDistance <= 10) {
                $biayaTransport += 0;
            }elseif ($generateDistance >= 10) {
                $biayaTransport += 60000 * $request->jml_personil;
            }


            $harga_user = $harga_total * 1.2 + $biayaMakan + $biayaMeeting + $biayaTransport;

            $dataOrderPengamananPerorang = [
                'tnos_service_id' => $request->tnos_service_id,
                'tnos_subservice_id' => $request->tnos_subservice_id,
                'user_id' => $request->user_id,
                'status_order' => '001',
                'payment_status' => 'ORDER',
                'needs' => $request->needs,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'time' => $request->datetime,
                'duration' => $request->duration,
                'location' => $request->location,
                'jml_personil' => $request->jml_personil,
                'order_total' => $harga_user,
                'pendapatan_mitra' => $pendapatan_mitra,
                'pendapatan_tnos' => $pendapatan_tnos,
                'start_lattitude' => $startLat,
                'start_longitude' => $startLang,
                'end_lattitude' => $endLat,
                'end_longitude' => $endLng,
                'jarak' => $generateDistance,
                'start_address' => $startAddress,
                'end_address' => $location,
                'biaya_tekhnical_meeting' => $biayaMeeting,
                'biaya_makan' => $biayaMakan,
                'biaya_transport' => $biayaTransport
            ];


            $order = Order::create($dataOrderPengamananPerorang);

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'data created successfully',
                'detail' => $order,
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong (Try Catch)',
                'error' => $e,
                'request_all' => $request->all()
            ], 500);
            throw $e; //sometime you want to rollback AND throw the exception
        }
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

        $OrderPengamananPerorang = Order::find($request->order_id);

        if (!$OrderPengamananPerorang) {
            return response()->json([
                'success' => false,
                'message' => 'Order id not found',
            ], 500);
        }


        if ($OrderPengamananPerorang->payment_status != 'ORDER') {
            return response()->json([
                'success' => false,
                'message' => 'there is something wrong',
            ], 500);
        }

        // $user = User::find($OrderPengamananPerorang->user_id);
        $no =  $OrderPengamananPerorang->phone;
        if (substr($no, 0, 1) == '0') {
            $no_baru = '+62' . substr($no, 1);
            $no = $no_baru;
        } else {
            $no =  $OrderPengamananPerorang->phone;
        }
        // mmbr_name
        // mmbr_email
        // mmbr_phone

        $params = [
            'external_id' => 'TNOS-PK-' . time(),
            'amount' => $OrderPengamananPerorang->order_total,
            'description' => $OrderPengamananPerorang->needs,
            'invoice_duration' => 3600,
            'customer' => [
                'given_names' =>  $OrderPengamananPerorang->name,
                'email' => $OrderPengamananPerorang->email,
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
                'invoice_expired' => [
                    'whatsapp',
                    // 'sms',
                    // 'email',
                    // 'viber'
                ]
            ],
            'success_redirect_url' => 'https://app.tnosworld.com/payment/custom/success/' . $OrderPengamananPerorang->id,
            'failure_redirect_url' => 'https://app.tnosworld.com/payment/failure/' . $OrderPengamananPerorang->id,
            'currency' => 'IDR',
            'items' => [
                [
                    'name' => 'Pengamanan Korporat',
                    'quantity' => 1,
                    'price' => $OrderPengamananPerorang->order_total,
                    'category' => 'Jasa Pengamanan',
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

        $OrderPengamananPerorang->payment_status = 'UNPAID';
        $OrderPengamananPerorang->invoice_id = $createInvoice['id'];
        $OrderPengamananPerorang->external_id = $createInvoice['external_id'];
        $OrderPengamananPerorang->expiry_date = Carbon::now()->addMinutes(60);
        $OrderPengamananPerorang->update();


        return response()->json([
            'success' => true,
            'message' => 'Success create invoice',
            'order' =>  $OrderPengamananPerorang,
        ], 200);
    }
}
