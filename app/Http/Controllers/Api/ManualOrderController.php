<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class ManualOrderController extends Controller
{
    protected $order;

    public function __construct()
    {
        $this->order = new Order();
    }

    public function index()
    {
        try {

            DB::beginTransaction();

            $order = Order::where("tnos_service_id", "3")
            ->where("tnos_subservice_id", "9")
            ->orderBy("created_at", "ASC")
            ->get();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Get Data Success",
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

    public function store(Request $request)
    {
        try {

            DB::beginTransaction();

            $date_ym = date('ym');
            $date_between = [date('Y-m-01 00:00:00'), date('Y-m-t 23:59:59')];

            $dataOrders = Order::select('code')
            ->where('type', 'deka')
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

            $prefix_invoice = "B2B" . "-" . Carbon::parse($request->created_at)->format("mY");
            $tnos_invoice_id = IdGenerator::generate(['table' => 'b2b_orders', 'field' => 'tnos_invoice_id', 'length' => 14, 'prefix' => $prefix_invoice, 'reset_on_prefix_change' => true]);

            $dataRequest = [
                'tnos_invoice_id' => $tnos_invoice_id,
                'tnos_service_id' => $request->tnos_service_id,
                'tnos_subservice_id' => $request->tnos_subservice_id,
                'external_id' => 'TNOS-PL-' . time(),
                'needs' => $request->needs,
                'code'  => 'No.2023/DLINV/' . $date_ym . $nowcode,
                'tracking_status'   => 'Belum Bayar',
                'user_id' => $request->user_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'file_document' => null,
                'name_badan_hukum' => $request->name_badan_hukum ? json_encode($request->name_badan_hukum) : null,
                'modal_dasar' => $request->modal_dasar ? $request->modal_dasar : 0,
                'modal_disetor' => $request->modal_disetor ? $request->modal_disetor : 0,
                'alamat_badan_hukum' => $request->alamat_badan_hukum ? json_encode($request->alamat_badan_hukum) : $request->alamat_badan_hukum,
                'pemegang_saham' => $request->pemegang_saham ? json_encode($request->pemegang_saham) : null,
                'susunan_direksi' => $request->susunan_direksi ? json_encode($request->susunan_direksi) : null,
                'bidang_usaha' => $request->bidang_usaha ? json_encode($request->bidang_usaha) : null,
                'email_badan_hukum' => $request->email_badan_hukum ? $request->email_badan_hukum : 0,
                'phone_badan_hukum' => $request->phone_badan_hukum ? $request->phone_badan_hukum : 0,
                'klasifikasi' => $request->klasifikasi ? json_encode($request->klasifikasi) : null,
                'order_total' => $request->order_total,
                'pendapatan_mitra' => 0,
                'pendapatan_tnos' => 0,
                'status_order' => '001',
                'payment_status' => 'ORDER',
                'payment_method' => 'BANK_TRANSFER',
                'payment_channel' => $request->payment_channel,
                'type'  => "tnos",
                'partner_id' => empty($request->partner["value"]) ? 0 : $request->partner['value'],
                'pendapatan_partner' => 0,
                'created_at' => $request->created_at,
                'paid_at' => Carbon::parse($request->paid_at)->format('Y-m-d H:i:s')
            ];

            $order = Order::create($dataRequest);

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

    public function edit($id)
    {
        try {

            DB::beginTransaction();

            $order = $this->order
            ->select("id", "created_at", "paid_at", "user_id", "name", "email", "phone", "order_total", "needs", "payment_channel")
            ->where("id", $id)
            ->first();

            $orderArray = $order->toArray();
            $orderArray['created_at'] = Carbon::parse($order->created_at)->format('Y-m-d H:i:s');

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Get Data By ID Success",
                "data" => $orderArray
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        try {

            DB::beginTransaction();

            $manualOrder = $this->order->findOrFail($id);

            $data = $request->all();

            $manualOrder->update($data);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Update Data Success"
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function uploadBukti(Request $request)
    {
        try {

            DB::beginTransaction();

            $data = [];
            if ($request->hasFile("file_document")) {
                $getImage = $request->file('file_document');
                $imageName = rand(10000000, 99999999) . time() . '.' . $getImage->getClientOriginalExtension();
                $path = public_path() . '/images/' . "PL" . '/';
                $imagePath = URL::to('/') . '/images/' . "PL" . '/' . $imageName;

                $getImage->move($path, $imageName);

                $data = [
                    "image_name" => $imageName,
                    "image_url" => $imagePath,
                    "mime" => $getImage->getClientMimeType(),
                ];
            }

            $orderData = $this->order->where("id", $request->id)->first();

            $orderData->update([
                "payment_status" => "PAID",
                "paid_amount" => $orderData->order_total,
                "status_order" => "002",
                "file_document" => json_encode([$data])
            ]);

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Upload Success"
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                "status" => false,
                "message" => $e->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {

            DB::beginTransaction();

            $order = $this->order->where("id", $id)->first();

            $fileDocument = json_decode($order->file_document, true);

            if (!empty($fileDocument) && isset($fileDocument[0]['image_url'])) {
                $imageUrl = $fileDocument[0]['image_url'];

                $filePath = parse_url($imageUrl, PHP_URL_PATH);
                $fullPath = public_path($filePath);

                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            $order->delete();

            DB::commit();

            return response()->json([
                "status" => true,
                "message" => "Delete Data Success"
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
