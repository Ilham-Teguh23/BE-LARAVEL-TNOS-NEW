<?php

namespace App\Http\Controllers\Api\Callback;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\Send\PaymentController;
use App\Models\OrderStatusDeka;
use Illuminate\Http\Request;
use Xendit\Xendit;
use App\Models\PaymentXendit;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Support\Facades\Log;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;

class VirtualAccountCallbackController extends Controller
{
    protected $serverKey;
    protected $callback_token;

    public function __construct()
    {
        $this->serverKey = config("xendit.xendit_production_key");
        $this->callback_token = config("xendit.xendit_development_callback_token");
    }

    public function callbackVirtualAccount(Request $request)
    {
        Xendit::setApiKey($this->serverKey);
        // Ini akan menjadi Token Verifikasi Callback Anda yang dapat Anda peroleh dari dasbor.
        // Pastikan untuk menjaga kerahasiaan token ini dan tidak mengungkapkannya kepada siapa pun.
        // Token ini akan digunakan untuk melakukan verfikasi pesan callback bahwa pengirim callback tersebut adalah Xendit
        $xenditXCallbackToken = $this->callback_token;
        $reqHeaders = $request->header('x-callback-token');;
        $xIncomingCallbackTokenHeader = isset($reqHeaders) ? $reqHeaders : "";

        if ($xIncomingCallbackTokenHeader) {
            if ($request->payment_id) {
                $prefix = "TCP" . '-' . date('mY');

                $invoice_id = IdGenerator::generate(['table' => 'payment_xendits', 'field' => 'invoice_id', 'length' => 14, 'prefix' => $prefix, 'reset_on_prefix_change' => true]);

                $payment = PaymentXendit::where('xendit_id', $request->callback_virtual_account_id)->first();
                $payment->invoice_id = $invoice_id;
                $payment->status = "SETTLED";
                $payment->others = json_encode([
                    'callback_virtual_account_id' => $request->callback_virtual_account_id,
                    'payment_id' => $request->payment_id,
                    'transaction_timestamp' => $request->transaction_timestamp,
                    'updated' => $request->updated,
                    'created' => $request->created,
                ]);
                $payment->update();

                $this->update_status_payment($request->external_id, $request);

                $this->_SuccessStore($request);

                $email = new PaymentController();
                $email->paymentSuccessEmail($payment);


                return response()->json([
                    'success' => false,
                    'message' => "Payment Succeeded Successfully",
                    'payment' => $payment
                ], 200);
            } else {
                $payment = PaymentXendit::where('xendit_id', $request->callback_virtual_account_id)->first();
                $payment->status = "EXPIRED";
                $payment->others = json_encode([
                    // 'status' => $request->status,
                    'payment_id' => $request->payment_id,
                    'transaction_timestamp' => $request->transaction_timestamp,
                    'updated' => $request->updated,
                    'created' => $request->created,
                ]);
                $payment->update();


                return response()->json([
                    'success' => false,
                    'message' => "Payment Succeeded Successfully",
                    'payment' => $payment
                ], 200);
            }
        } else {
            return response()->json([
                'success' => false,
                'message' => "Callback token is invalid"
            ], 401);
        }
    }

    public static function update_status_payment($id, $request)
    {
        $externalId = $id;

        $page = explode("-", $externalId);
        $getExternalId = $page[0];
        $getExternalId .= "-" . $page[1];

        $tracking = OrderStatusDeka::create([
            'external_id'  => $getExternalId,
            'status'    => $request->status,
            'datetime'  => date('Y-m-d H:i:s'),
            'deskripsi' => 'data telah di perbarui di waktu ' . date('Y-m-d H:i:s')
        ]);

        return $tracking;
    }


    public function _SuccessStore($req)
    {

        $jsondata = json_encode($req);
        // Log::info($id);
        //Log::info($req);
        // $url = "https://api-dev.tnos.world/payment/callback";
        $url = "https://api.tnosworld.id/payment/callback";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Content-Type: application/json",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $resdata = '{
            "order_id":"' . $req['external_id'] . '",
            "business_id":"' . $req['owner_id'] . '",
            "vendor_id":"2",
            "transaction_id":"' . $req['id'] . '",
            "event":"",
            "currency":"' . $req['currency'] . '",
            "gross_amount":"' . $req['amount'] . '",
            "channel_code":"' . $req['bank_code'] . '",
            "checkout_method":"VIRTUAL_ACCOUNT",
            "created":"' . $req['created'] . '",
            "status":"SETTLED",
            "another":[' . $jsondata . ']
        }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $resdata);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $resStore = json_decode($resp);
        Log::info("START VIRTUAL ACCOUNT ---------------------------------------------------------------------------");
        Log::info($resp);
        Log::info("END VIRTUAL ACCOUNT ---------------------------------------------------------------------------");
        return true;
    }
}
