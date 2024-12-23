<?php

namespace App\Http\Controllers\Api\Xendit;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Xendit\Xendit;

class VirtualAccountController extends Controller
{

    public function createVa(Request $request)
    {
        Xendit::setApiKey('xnd_development_8z7k7GsiAuXdW4E2jGoWsDRwSbpdZLkEWmYfB9R62AqPNzymDSwqZGzTEKqEFk');

        $params = [
            "external_id" => 'VA_fixed-' . time(),
            "bank_code" => 'BNI',
            "name" => $request->name
        ];


        $createVA = \Xendit\VirtualAccounts::create($params);
        return response()->json([
            'success' => true,
            'message' => 'Success create virtual account',
            'virtual_account' =>  $createVA,
        ], 200);
    }
    public function VaPayment(Request $request)
    {
        Xendit::setApiKey('xnd_development_8z7k7GsiAuXdW4E2jGoWsDRwSbpdZLkEWmYfB9R62AqPNzymDSwqZGzTEKqEFk');


        $id = '6389802b3864ff4b2e677b11';

        $updateParams = [
            "transfer_amount" => 100009,
            "bank_account_number" => "8808999925539902",
            "bank_code" => "BNI"
        ];

        $updateVA = \Xendit\VirtualAccounts::update($id, $updateParams);

        return response()->json([
            'success' => true,
            'message' => 'Success pembayaran virtual account payment',
            'updateVA' =>  $updateVA,
            'params' => $updateParams
        ], 200);
    }
}
