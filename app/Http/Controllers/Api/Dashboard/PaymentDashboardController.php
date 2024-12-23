<?php

namespace App\Http\Controllers\Api\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PaymentXendit;
use Illuminate\Http\Request;

class PaymentDashboardController extends Controller
{
    public function getPayment()
    {
        $payment = PaymentXendit::orderBy('updated_at', 'desc')->get();

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
}
