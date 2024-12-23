<?php

namespace App\Http\Controllers\Api\Callback\Xendit;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Email\Send\PaymentController;
use App\Models\Order;
use GuzzleHttp\Client;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;

class InvoiceCallbackController extends Controller
{
    var $apiInstance = null;

    public function __construct()
    {
        Configuration::setXenditKey("xnd_development_7kWjixnClUSbCEVa35SjG7etTZpEWWN32V9jAOn1C22t6Uq8he1uJPKj3kYg4U04:");
        $this->apiInstance = new InvoiceApi();
    }

    public function callbackVirtualAccount(Request $request)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . base64_encode("xnd_development_7kWjixnClUSbCEVa35SjG7etTZpEWWN32V9jAOn1C22t6Uq8he1uJPKj3kYg4U04:")
            ])->get("https://api.xendit.co/v2/invoices/" . $request->invoice_id);

            $result = $this->apiInstance->getInvoices(null, $request->external_id);
            $payment = Order::where("external_id", $request->external_id)->firstOrFail();

            $prefix = "B2B" . '-' . date('mY');
            $tnos_invoice_id = IdGenerator::generate(['table' => 'b2b_orders', 'field' => 'tnos_invoice_id', 'length' => 14, 'prefix' => $prefix, 'reset_on_prefix_change' => true]);
            $payment->tnos_invoice_id = $tnos_invoice_id;
            $payment->payment_channel = $response->json()[0]['payment_channel'];
            $payment->status_order = "RUN";
            $payment->payment_method = $result[0]['payment_method'];
            $payment->paid_amount = $result[0]['amount'];
            $payment->paid_at = date("Y-m-d H:i:s");
            $payment->payment_status = strtolower($result[0]['status']);
            $payment->save();

            $email = new PaymentController();
            $email->paymentSuccessEmail($payment);

            return response()->json([
                "message" => "Payment anda telah di proses",
                "data" => $result
            ]);

        } catch (\Exception $e) {
            return response()->json([
                "message" => $e->getMessage(),
                "status" => false
            ], 500);
        }
    }
}
