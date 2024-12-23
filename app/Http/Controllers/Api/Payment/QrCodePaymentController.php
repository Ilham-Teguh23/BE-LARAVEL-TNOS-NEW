<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Api\Config\ConfigXenditController;
use App\Http\Controllers\Controller;
use App\Models\PaymentXendit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QrCodePaymentController extends Controller
{
    public function getQrCode($external_id)
    {
        $xendit = new ConfigXenditController();
        $xendit->apiKeyXendit();

        $qr_code = \Xendit\QRCode::get($external_id);
        return $qr_code;
    }
    public function createQrCodes(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'amount' => 'required',
            'items' => 'required',
            'id' => 'required'
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages()
            ], 402);
        }



        $xendit = new ConfigXenditController();
        $xendit->apiKeyXendit();


        try {

            $params = [
                'reference_id' => $request->id . "-" . time(),
                'type' => 'DYNAMIC',
                // 'callback_url' => 'https://webhook.site',
                "currency" => "IDR",
                "api_version" => "2022-07-31",
                'amount' => $request->amount,
                'expires_at' => Carbon::now()->addMinutes(30)
            ];


            $created_qr_code = \Xendit\QRCode::create($params);

            $data = [
                'xendit_id' => $created_qr_code['id'],
                'business_id' => $created_qr_code['business_id'],
                'reference_id' => $created_qr_code['reference_id'],
                'amount' => $request->amount,
                'status' => $created_qr_code['status'],
                'description' => "percobaan",
                'customer' => json_encode([
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                ]),
                'items' => json_encode($request->items),
                "payment_method" => "QR_CODE",
                'actions' => json_encode([
                    "qr_string" => $created_qr_code['qr_string'],
                ]),
                'expiration_date' => $created_qr_code['expires_at'],
                'others' => null,
            ];

            $payment = PaymentXendit::create($data);


            return response()->json([
                "status" => true,
                "message" => "Create virtual Account Mandiri Successfully",
                'qr_code' => $created_qr_code,
                'payment' => $payment,
            ]);
        } catch (\Xendit\Exceptions\InvalidArgumentException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ? $e->getMessage() : "Something wrong with the request",
            ], 403);
        } catch (\Xendit\Exceptions\ApiException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ? $e->getMessage() : "Something wrong with the request",
                'error_code' => $e->getErrorCode(),
            ], 403);
        }
    }
}
// update 
