<?php

namespace App\Http\Controllers\Api\Payment;

use App\Http\Controllers\Api\Config\ConfigXenditController;
use App\Http\Controllers\Controller;
use App\Models\PaymentXendit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class EwalletPaymentControler extends Controller
{

    public function getPaymentById($id)
    {
        $payment = PaymentXendit::find($id);

        if (!$payment) {
            return response()->json([
                'success' => false,
                'message' => 'Data transactions not found',

            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data transactions',
            'payment' => $payment,
        ], 200);
    }


    public function createEwalletOvo(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
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
                'currency' => 'IDR',
                'amount' => $request->amount,
                'checkout_method' => 'ONE_TIME_PAYMENT',
                'channel_code' => 'ID_OVO',
                'channel_properties' => [
                    "mobile_number" => $request->phone
                ],
                "metadata" => [
                    "branch_area" => null,
                    "branch_city" => null
                ]
            ];

            $createEWalletCharge = \Xendit\EWallets::createEWalletCharge($params);

            $data = [
                'xendit_id' => $createEWalletCharge['id'],
                'business_id' => $createEWalletCharge['business_id'],
                'reference_id' => $createEWalletCharge['reference_id'],
                'amount' => $request->amount,
                'status' => $createEWalletCharge['status'],
                'description' => $request->description ? $request->description : "-",
                'customer' => json_encode([
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                ]),
                'items' => $request->items,
                'actions' => $createEWalletCharge['actions'] ? json_encode($createEWalletCharge['actions']) : null,
                'payment_method' => $createEWalletCharge['checkout_method'],
                'payment_channel' => $createEWalletCharge['channel_code'],
                'others' => null
            ];

            $payment = PaymentXendit::create($data);

            return response()->json([
                "status" => true,
                "message" => "Create charger ewallet OVO Successfully",
                'charger' => $createEWalletCharge,
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

    public function createEwalletDana(Request $request)
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
                'currency' => 'IDR',
                'amount' => $request->amount,
                'checkout_method' => 'ONE_TIME_PAYMENT',
                'channel_code' => 'ID_DANA',
                'channel_properties' => [
                    "success_redirect_url" => "https://app.tnosworld.com//payment/notification/success/" . 'TNOS-' . time(),
                ],
                "metadata" => [
                    "branch_area" => null,
                    "branch_city" => null
                ]
            ];

            $createEWalletCharge = \Xendit\EWallets::createEWalletCharge($params);

            $data = [
                'xendit_id' => $createEWalletCharge['id'],
                'business_id' => $createEWalletCharge['business_id'],
                'reference_id' => $createEWalletCharge['reference_id'],
                'amount' => $request->amount,
                'status' => $createEWalletCharge['status'],
                'description' => $request->description ? $request->description : "-",
                'customer' => json_encode([
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                ]),
                'items' => $request->items,
                'actions' => $createEWalletCharge['actions'] ? json_encode($createEWalletCharge['actions']) : null,
                'payment_method' => $createEWalletCharge['checkout_method'],
                'payment_channel' => $createEWalletCharge['channel_code'],
                'others' => null
            ];

            $payment = PaymentXendit::create($data);

            return response()->json([
                "status" => true,
                "message" => "Create charger ewallet DANA Successfully",
                'charger' => $createEWalletCharge,
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

    public function createEwalletShopeepay(Request $request)
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
                'currency' => 'IDR',
                'amount' => $request->amount,
                'checkout_method' => 'ONE_TIME_PAYMENT',
                'channel_code' => 'ID_SHOPEEPAY',
                'channel_properties' => [
                    "success_redirect_url" => "https://app.tnosworld.com//payment/notification/success/" . 'TNOS-' . time(),
                ],
                "metadata" => [
                    "branch_area" => null,
                    "branch_city" => null
                ]
            ];

            $createEWalletCharge = \Xendit\EWallets::createEWalletCharge($params);

            $data = [
                'xendit_id' => $createEWalletCharge['id'],
                'business_id' => $createEWalletCharge['business_id'],
                'reference_id' => $createEWalletCharge['reference_id'],
                'amount' => $request->amount,
                'status' => $createEWalletCharge['status'],
                'description' => $request->description ? $request->description : "-",
                'customer' => json_encode([
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                ]),
                'items' => $request->items,
                'actions' => $createEWalletCharge['actions'] ? json_encode($createEWalletCharge['actions']) : null,
                'payment_method' => $createEWalletCharge['checkout_method'],
                'payment_channel' => $createEWalletCharge['channel_code'],
                'others' => null
            ];

            $payment = PaymentXendit::create($data);

            return response()->json([
                "status" => true,
                "message" => "Create charger ewallet Shopeepay Successfully",
                'charger' => $createEWalletCharge,
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

    public function createEwalletAstrapay(Request $request)
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
                'currency' => 'IDR',
                'amount' => $request->amount,
                'checkout_method' => 'ONE_TIME_PAYMENT',
                'channel_code' => 'ID_ASTRAPAY',
                'channel_properties' => [
                    "success_redirect_url" => "https://app.tnosworld.com/payment/notification/success/" . 'TNOS-' . time(),
                    "failure_redirect_url" => "https://app.tnosworld.com/payment/notification/failure/" . 'TNOS-' . time(),
                ],
                "metadata" => [
                    "branch_area" => null,
                    "branch_city" => null
                ]
            ];

            $createEWalletCharge = \Xendit\EWallets::createEWalletCharge($params);

            $data = [
                'xendit_id' => $createEWalletCharge['id'],
                'business_id' => $createEWalletCharge['business_id'],
                'reference_id' => $createEWalletCharge['reference_id'],
                'amount' => $request->amount,
                'status' => $createEWalletCharge['status'],
                'description' => $request->description ? $request->description : "-",
                'customer' => json_encode([
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                ]),
                'items' => $request->items,
                'actions' => $createEWalletCharge['actions'] ? json_encode($createEWalletCharge['actions']) : null,
                'payment_method' => $createEWalletCharge['checkout_method'],
                'payment_channel' => $createEWalletCharge['channel_code'],
                'others' => null
            ];

            $payment = PaymentXendit::create($data);

            return response()->json([
                "status" => true,
                "message" => "Create charger ewallet LinkAja Successfully",
                'charger' => $createEWalletCharge,
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
    public function createEwalletLinkaja(Request $request)
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
                'currency' => 'IDR',
                'amount' => $request->amount,
                'checkout_method' => 'ONE_TIME_PAYMENT',
                'channel_code' => 'ID_LINKAJA',
                'channel_properties' => [
                    "success_redirect_url" => "https://app.tnosworld.com/payment/notification/success/" . 'TNOS-' . time(),
                ],
                "metadata" => [
                    "branch_area" => null,
                    "branch_city" => null
                ]
            ];

            $createEWalletCharge = \Xendit\EWallets::createEWalletCharge($params);

            $data = [
                'xendit_id' => $createEWalletCharge['id'],
                'business_id' => $createEWalletCharge['business_id'],
                'reference_id' => $createEWalletCharge['reference_id'],
                'amount' => $request->amount,
                'status' => $createEWalletCharge['status'],
                'description' => $request->description ? $request->description : "-",
                'customer' => json_encode([
                    "name" => $request->name,
                    "email" => $request->email,
                    "phone" => $request->phone,
                ]),
                'items' => $request->items,
                'actions' => $createEWalletCharge['actions'] ? json_encode($createEWalletCharge['actions']) : null,
                'payment_method' => $createEWalletCharge['checkout_method'],
                'payment_channel' => $createEWalletCharge['channel_code'],
                'others' => null
            ];

            $payment = PaymentXendit::create($data);

            return response()->json([
                "status" => true,
                "message" => "Create charger ewallet LinkAja Successfully",
                'charger' => $createEWalletCharge,
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
