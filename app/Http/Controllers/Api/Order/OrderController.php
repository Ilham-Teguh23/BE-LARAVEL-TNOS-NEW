<?php

namespace App\Http\Controllers\Api\Order;

use App\Http\Controllers\Controller;
use App\Models\PaymentXendit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function create(Request $request)
    {
        //valid credential
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'amount' => 'required',
            'items' => 'required',
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
            'reference_id' => null,
            'amount' => $request->amount,
            'status' => "PENDING",
            'description' => $request->description ? $request->description : "-",
            'customer' => json_encode([
                "name" => $request->name,
                "email" => $request->email,
                "phone" => $request->phone,
            ]),
            'items' => $request->items,
            'actions' => null,
            'payment_method' => null,
            'payment_channel' => null,
            'others' => null
        ];

        $payment = PaymentXendit::create($data);

        return response()->json([
            "status" => true,
            "message" => "Create Payment Successfully",
            'payment' => $payment,
        ]);
    }
}
