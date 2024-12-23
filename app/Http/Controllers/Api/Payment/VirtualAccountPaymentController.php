<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Api\Config\ConfigXenditController;
use App\Http\Controllers\Controller;
use App\Models\PaymentXendit;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VirtualAccountPaymentController extends Controller
{
    public function getListBank()
    {
        $xendit = new ConfigXenditController();
        $xendit->apiKeyXendit();

        $getVABanks = \Xendit\VirtualAccounts::getVABanks();
        if (!count($getVABanks)) {
            return response()->json([
                'success' => false,
                'message' => "Something went wrong"
            ], 402);
        }

        return response()->json([
            'success' => true,
            'message' => "List bank successfully",
            'list_bank' => $getVABanks,
        ], 200);
    }
    public function createCreateVirtualAccount(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'required|numeric',
            'amount' => 'required',
            'items' => 'required',
            'bank_code' => 'required',
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
                "external_id" => $request->id . "-" . time(),
                "bank_code" => $request->bank_code,
                "name" => $request->name,
                "expected_amount" => $request->amount,
                'is_single_use' => true,
                'is_closed' => true,
                'expiration_date' => Carbon::now()->addMinutes(30),
            ];

            $createVA = \Xendit\VirtualAccounts::create($params);

            $data = [
                'xendit_id' => $createVA['id'],
                'business_id' => $createVA['owner_id'],
                'reference_id' => $createVA['external_id'],
                'amount' => $request->amount,
                'status' => $createVA['status'],
                'merchant_code' => $createVA['merchant_code'],
                'bank_code' => $createVA['bank_code'],
                'description' => "percobaan",
                'customer' => json_encode([
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                ]),
                'items' => $request->items,
                'actions' => "-",
                'country' => $createVA['country'],
                'account_number' => $createVA['account_number'],
                'is_closed' => $createVA['is_closed'],
                'is_single_use' => $createVA['is_single_use'],
                'payment_method' => 'VIRTUAL_ACCOUNT',
                'payment_channel' => $createVA['bank_code'],
                'expiration_date' => $createVA['expiration_date'],
                'others' => "-",
            ];

            $payment = PaymentXendit::create($data);

            return response()->json([
                "status" => true,
                "message" => "Create virtual Account Mandiri Successfully",
                'charger' => $createVA,
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
