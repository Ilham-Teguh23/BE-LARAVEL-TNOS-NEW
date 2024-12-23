<?php

namespace App\Http\Controllers\Api\Callback;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xendit\Xendit;
use App\Models\PaymentXendit;
use Haruncpi\LaravelIdGenerator\IdGenerator;

class CreditCardsCallbackController extends Controller
{
    protected $serverKey;
    protected $callback_token;


    public function __construct()
    {
        $this->serverKey = config("xendit.xendit_production_key");
        $this->callback_token = config("xendit.xendit_development_callback_token");
    }

    public function otentikasiCard(Request $request)
    {
        Xendit::setApiKey($this->serverKey);
        // Ini akan menjadi Token Verifikasi Callback Anda yang dapat Anda peroleh dari dasbor.
        // Pastikan untuk menjaga kerahasiaan token ini dan tidak mengungkapkannya kepada siapa pun.
        // Token ini akan digunakan untuk melakukan verfikasi pesan callback bahwa pengirim callback tersebut adalah Xendit
        $xenditXCallbackToken = $this->callback_token;
        $reqHeaders = $request->header('x-callback-token');;
        $xIncomingCallbackTokenHeader = isset($reqHeaders) ? $reqHeaders : "";

        if ($xIncomingCallbackTokenHeader) {

            $data = [
                'xendit_id' => 'id',
                'business_id' => 'otentikasi',
                'reference_id' => 'reference_id',
                'amount' => 123456,
                'status' => 'status',
                'description' => "-",
                'customer' => json_encode([
                    "name" => "vanama",
                    "email" => "vanama",
                    "phone" => "vanama",
                ]),
                'items' => "vanama",
                'actions' => 'actions',
                'payment_method' => 'checkout_method',
                'payment_channel' => $request->id,
                'others' => json_encode($request->data)
            ];

            $payment = PaymentXendit::create($data);
            return $payment;
        } else {
            return response()->json([
                'success' => false,
                'message' => "Callback token is invalid"
            ], 401);
        }
    }

    public function tokenisasiCard(Request $request)
    {
        Xendit::setApiKey($this->serverKey);
        // Ini akan menjadi Token Verifikasi Callback Anda yang dapat Anda peroleh dari dasbor.
        // Pastikan untuk menjaga kerahasiaan token ini dan tidak mengungkapkannya kepada siapa pun.
        // Token ini akan digunakan untuk melakukan verfikasi pesan callback bahwa pengirim callback tersebut adalah Xendit
        $xenditXCallbackToken = $this->callback_token;
        $reqHeaders = $request->header('x-callback-token');;
        $xIncomingCallbackTokenHeader = isset($reqHeaders) ? $reqHeaders : "";

        if ($xIncomingCallbackTokenHeader == $xenditXCallbackToken) {

            $data = [
                'xendit_id' => 'id',
                'business_id' => 'tokenisasi',
                'reference_id' => 'reference_id',
                'amount' => 123456,
                'status' => 'status',
                'description' => "-",
                'customer' => json_encode([
                    "name" => "vanama",
                    "email" => "vanama",
                    "phone" => "vanama",
                ]),
                'items' => "vanama",
                'actions' => 'actions',
                'payment_method' => 'checkout_method',
                'payment_channel' => $request->id,
                'others' => json_encode($request->data)
            ];

            $payment = PaymentXendit::create($data);
            return $payment;
        } else {
            return response()->json([
                'success' => false,
                'message' => "Callback token is invalid"
            ], 401);
        }
    }
}
