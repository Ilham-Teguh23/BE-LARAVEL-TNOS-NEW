<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Api\Config\ConfigXenditController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\Send\PaymentController;
use App\Models\PaymentXendit;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class CreditCardPaymentController extends Controller
{


    public function create(Request $request)
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

        $data = [
            'xendit_id' => null,
            'business_id' => null,
            'reference_id' =>  $request->id. "-" . time(),
            'amount' => $request->amount,
            'status' => 'CREATED',
            'description' => $request->description ? $request->description : "-",
            'customer' => json_encode([
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
            ]),
            'items' => $request->items,
            'actions' =>  null,
            'payment_method' => 'CREDIT_CARD',
            'payment_channel' => null,
            'others' => null
        ];

        $payment = PaymentXendit::create($data);


        return response()->json([
            "status" => true,
            "message" => "Create Credit or Debit Card Successfully",
            'payment' => $payment,
        ]);
    }

    public function updateTokenToPaymentData(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'authentication_id' => 'required',
            'token_id' => 'required',
            'status' => 'required',
            'card_info' => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages()
            ], 402);
        }

        $payment = PaymentXendit::find($request->id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'ID payment not found',
            ], 402);
        }

        if (
            $payment->status == "APPROVED" ||
            $payment->status == "VERIFIED"
        ) {
            return response()->json([
                'success' => false,
                'message' => 'You havent access for this acction, your status is ' . $payment->status,
            ], 402);
        }

        DB::beginTransaction();
        try {
            $payment->authentication_id = $request->authentication_id;
            $payment->token_id = $request->token_id;
            $payment->status = $request->status;
            $payment->card_info = $request->card_info;
            $payment->update();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Update successfully',
                'payment' => $payment,
            ], 200);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'there is something wrong (Try Catch)',
                'error' => $e,
            ], 403);
        }
    }

    public function createCharge(Request $request)
    {
        $xendit = new ConfigXenditController();
        $xendit->apiKeyXendit();

        //valid credential
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            // 'authentication_id' => 'required',
            'token_id' => 'required',
            'status' => 'required',
            'amount' => 'required',
            // 'card_cvn' => 'required',
        ]);

        //Send failed response if request is not valid
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->messages()
            ], 402);
        }
        $payment = PaymentXendit::find($request->id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'ID payment not found',
            ], 402);
        }

        if ($payment->amount != $request->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Amount is incorrect',
            ], 403);
        }

        try {

            $params = [
                'token_id' => $request->token_id,
                'external_id' => $payment->reference_id,
                // 'authentication_id' => $request->authentication_id,
                'amount' => $request->amount,
                // 'card_cvn' => $request->card_cvn,
                // 'capture' => false
            ];



            $createCharge = \Xendit\Cards::create($params);

            $prefix = "TCP" . '-' . date('mY');
            $invoice_id = IdGenerator::generate(['table' => 'payment_xendits', 'field' => 'invoice_id', 'length' => 14, 'prefix' => $prefix, 'reset_on_prefix_change' => true]);

            $payment->invoice_id = $invoice_id;
            $payment->xendit_id = $createCharge['id'];
            $payment->business_id = $createCharge['business_id'];
            // $payment->reference_id = $createCharge['external_id'];
            $payment->status = $createCharge['status'];
            $payment->others = json_encode([
                "merchant_reference_code" => $createCharge['merchant_reference_code'],
                "eci" => $createCharge['eci'],
                "authorized_amount" => $createCharge['authorized_amount'],
                "capture_amount" => $createCharge['capture_amount'],
                "masked_card_number" => $createCharge['masked_card_number'],
                "card_type" => $createCharge['card_type'],
                "charge_type" => $createCharge['charge_type'],
                "card_brand" => $createCharge['card_brand'],
                "bank_reconciliation_id" => $createCharge['bank_reconciliation_id'],
                "created" => $createCharge['created'],
            ]);
            $payment->update();

            $this->_SuccessStore($createCharge);
            $email = new PaymentController();
            $email->paymentSuccessEmail($payment);

            return response()->json([
                'success' => true,
                'message' => 'Create Charge successfully',
                'payment' => $payment,
            ], 200);
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


    public function _SuccessStore($req){

        Log::info($req);
        $jsondata = json_encode($req);
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
            "order_id":"'.$req['external_id'].'",
            "business_id":"'.$req['business_id'].'",
            "vendor_id":"2",
            "transaction_id":"'.$req['id'].'",
            "event":"",
            "currency":"'.$req['currency'].'",
            "gross_amount":"'.$req['capture_amount'].'",
            "channel_code":"'.$req['card_brand'].'",
            "checkout_method":"'.$req['card_type'].'",
            "created":"'.$req['created'].'",
            "status":"'.$req['status'].'",
            "another":['.$jsondata.']
        }';
        // $resdata = '{
        //     "business_id":"'.$req['business_id'].'",
        //     "merchant_id":"'.$req['merchant_reference_code'].'",
        //     "vendor_id":"2",
        //     "transaction_id":"'.$req['id'].'",
        //     "event":"'.$req['event'].'"
        //     "another":['.$jsondata.']
        // }';

        curl_setopt($curl, CURLOPT_POSTFIELDS, $resdata);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $resStore = json_decode($resp);
        Log::info("START CC ---------------------------------------------------------------------------");
        Log::info($resp);
        Log::info("END CC ---------------------------------------------------------------------------");
        return true;
    }
}
