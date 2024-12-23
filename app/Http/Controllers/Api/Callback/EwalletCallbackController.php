<?php

namespace App\Http\Controllers\Api\Callback;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\Send\PaymentController;
use App\Models\OrderStatusDeka;
use App\Models\PaymentXendit;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Xendit\Xendit;
use Illuminate\Support\Facades\Log;

class EwalletCallbackController extends Controller
{
    protected $serverKey;
    protected $callback_token;


    public function __construct()
    {
        $this->serverKey = config("xendit.xendit_production_key");
        $this->callback_token = config("xendit.xendit_development_callback_token");
    }

    public function callbackEwallet(Request $request)
    {
        Xendit::setApiKey($this->serverKey);
        // Ini akan menjadi Token Verifikasi Callback Anda yang dapat Anda peroleh dari dasbor.
        // Pastikan untuk menjaga kerahasiaan token ini dan tidak mengungkapkannya kepada siapa pun.
        // Token ini akan digunakan untuk melakukan verfikasi pesan callback bahwa pengirim callback tersebut adalah Xendit
        $xenditXCallbackToken = $this->callback_token;
        $reqHeaders = $request->header('x-callback-token');;
        $xIncomingCallbackTokenHeader = isset($reqHeaders) ? $reqHeaders : "";

        if ($xIncomingCallbackTokenHeader) {


            if ($request->data['status'] == 'SUCCEEDED') {

                $prefix = "TCP" . '-' . date('mY');

                $invoice_id = IdGenerator::generate(['table' => 'payment_xendits', 'field' => 'invoice_id', 'length' => 14, 'prefix' => $prefix, 'reset_on_prefix_change' => true]);

                $payment = PaymentXendit::where('xendit_id', $request->data['id'])->first();
                $payment->invoice_id = $invoice_id;
                $payment->status = $request->data['status'];
                $payment->others = json_encode([
                    'charge_amount' => $request->data['charge_amount'],
                    'capture_amount' => $request->data['capture_amount'],
                    'refunded_amount' => $request->data['refunded_amount'],
                    'channel_properties' => $request->data['channel_properties'],
                    'is_redirect_required' => $request->data['is_redirect_required'],
                    'callback_url' => $request->data['callback_url'],
                    'void_status' => $request->data['void_status'],
                    'voided_at' => $request->data['voided_at'],
                    'capture_now' => $request->data['capture_now'],
                    'payment_method_id' => $request->data['payment_method_id'],
                    'failure_code' => $request->data['failure_code'],
                    'basket' => $request->data['basket'],
                    'metadata' => $request->data['metadata'],
                    'created' => $request->data['created'],
                    'updated' => $request->data['updated'],
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
                $payment = PaymentXendit::where('xendit_id', $request->data['id'])->first();
                $payment->status = $request->data['status'];
                $payment->others = json_encode([
                    'charge_amount' => $request->data['charge_amount'],
                    'capture_amount' => $request->data['capture_amount'],
                    'refunded_amount' => $request->data['refunded_amount'],
                    'channel_properties' => $request->data['channel_properties'],
                    'is_redirect_required' => $request->data['is_redirect_required'],
                    'callback_url' => $request->data['callback_url'],
                    'void_status' => $request->data['void_status'],
                    'voided_at' => $request->data['voided_at'],
                    'capture_now' => $request->data['capture_now'],
                    'payment_method_id' => $request->data['payment_method_id'],
                    'failure_code' => $request->data['failure_code'],
                    'basket' => $request->data['basket'],
                    'metadata' => $request->data['metadata'],
                    'created' => $request->data['created'],
                    'updated' => $request->data['updated'],
                ]);
                $payment->update();

                return response()->json([
                    'success' => false,
                    'message' => "Payment Failed Successfully"
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

        $jsondata = json_encode($req->data);
        // Log::info($id);
        // Log::info($req);
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
            "order_id":"' . $req->data['reference_id'] . '",
            "business_id":"' . $req['business_id'] . '",
            "vendor_id":"2",
            "transaction_id":"' . $req->data['id'] . '",
            "event":"' . $req['event'] . '",
            "currency":"' . $req->data['currency'] . '",
            "gross_amount":"' . $req->data['capture_amount'] . '",
            "channel_code":"' . $req->data['channel_code'] . '",
            "checkout_method":"' . $req->data['checkout_method'] . '",
            "created":"' . $req->data['created'] . '",
            "status":"' . $req->data['status'] . '",
            "another":[' . $jsondata . ']
        }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $resdata);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $resStore = json_decode($resp);
        Log::info("START EWALLET ---------------------------------------------------------------------------");
        Log::info($resdata);
        Log::info($resp);
        Log::info("END EWALLET ---------------------------------------------------------------------------");
        return true;
    }
}
