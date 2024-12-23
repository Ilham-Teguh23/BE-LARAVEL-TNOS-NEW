<?php

namespace App\Http\Controllers\Email\Send;

use App\Http\Controllers\Carbon\Convert\TimeConvertController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Number\Convert\NumberConvertController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller
{
    public function paymentSuccessEmail($dataParse)
    {
        $jsonData = $dataParse;

        $payment = json_decode($jsonData);

        $number = new NumberConvertController();
        $paymentAmount = $number->convertNumberPrice($jsonData->order_total);

        $customer = $jsonData['name'];

        $customerName = $customer;
        $customerEmail = $jsonData->email;

        $paymentTime = $jsonData->updated_at;
        $time = new TimeConvertController();
        $times = $time->convertTimePaymentSuccess($paymentTime);

        $paymentChannel = "";
        switch ($payment->payment_method) {
            case "QR_CODE":
                $paymentChannel = "QR Code";
                break;
            case "CREDIT_CARD":
                $paymentChannel = "CREDIT CARD";
                break;
            case "EWALLET":
                switch ($payment->payment_channel) {
                    case 'LINKAJA':
                        $paymentChannel = 'E-Wallet LinkAja';
                        break;
                    case 'OVO':
                        $paymentChannel = "E-Wallet Ovo";
                        break;
                    case 'DANA':
                        $paymentChannel = "E-Wallet Dana";
                        break;
                    case 'SHOPEEPAY':
                        $paymentChannel = "E-Wallet Shoppepay";
                        break;
                    case 'ASTRAPAY':
                        $paymentChannel = "E-Wallet Astrapay";
                        break;
                    default:
                        $paymentChannel = "E-Wallet Tidak diketahui";
                        break;
                }
                break;
            case "BANK_TRANSFER":
                switch ($payment->payment_channel) {
                    case 'BNI':
                        $paymentChannel = 'Virtual Account BNI';
                        break;
                    case 'MANDIRI':
                        $paymentChannel = 'Virtual Account Mandiri';
                        break;
                    case 'PERMATA':
                        $paymentChannel = 'Virtual Account Permata';
                        break;
                    case 'PERMATA':
                        $paymentChannel = 'Virtual Account Permata';
                        break;
                    case 'SAHABAT_SAMPOERNA':
                        $paymentChannel = 'Virtual Account Sahabat Sampoerna';
                        break;
                    case 'BRI':
                        $paymentChannel = 'Virtual Account BRI';
                        break;
                    case 'BSI':
                        $paymentChannel = 'Virtual Account BSI';
                        break;
                    case 'BJB':
                        $paymentChannel = 'Virtual Account BJB';
                        break;
                    default:
                        $paymentChannel = "Virtual Account Tidak diketahui";
                        break;
                }
                break;
            default;
                break;
        }

        $email_template = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
          <head>
            <meta charset="UTF-8" />
            <meta http-equiv="X-UA-Compatible" content="IE=edge" />
            <meta name="viewport" content="width=device-width, initial-scale=1.0" />
            <title>Pembayaran Berhasil TNOS</title>
            <link
              rel="stylesheet"
              href="https://fonts.googleapis.com/icon?family=Material+Icons"
            />
            <style>
              * {
                font-family: robot, lato !important;
                font-size: large;
              }

              /* CSS styles... */
              table {
                border-collapse: collapse;
                border-spacing: 0;
              }
              tr {
                border-bottom: 1px solid rgb(171, 171, 171);
              }

              td,
              th {
                padding: 10px 0; /* Adjust the values as needed */
              }

              @media print {
                /* Hide unnecessary elements when printing */
                .action-button {
                  display: none;
                }
              }
            </style>
          </head>

          <body>
            <main style="text-align: center">
              <div
                style="
                  display: flex;
                  align-items: center;
                  justify-content: start;
                  border-bottom: 1px solid rgb(171, 171, 171);
                "
              >
                <img src="https://tnosworld.com/public/assets/email/tnospay/logo_loader.png" alt="" style="width: 150px" />
              </div>
              <br />
              <div style="display: block; align-items: center">
                <div style="text-align: center">
                  <b style="font-weight: 600; font-size: 16px; color: #434343"
                    >Pembayaran Berhasil</b
                  >
                </div>
                <br />
                <br />
                <div style="text-align: center">
                  <img
                    src="https://tnosworld.com/public/assets/email/tnospay/success.png"
                    alt=""
                    style="max-width: 220px; height: 150px"
                  />
                </div>
                <div style="padding: 10px">
                  <b
                    style="
                      text-align: center;
                      font-weight: 600;
                      font-size: 16px;
                      color: #434343;
                    "
                    >Kepada {$customerName}</b
                  >
                  <p
                    style="
                      text-align: justify;
                      font-weight: 400;
                      font-size: 14px;
                      color: #434343;
                    "
                  >
                    Pembayaran Anda telah berhasil. <br> Terima kasih sudah menggunakan TNOS. Kami berharap Anda menikmati pelayanan Kami dan semoga hari Anda menyenangkan.
                  </p>
                </div>

                <div style="border: 1px solid rgb(171, 171, 171); padding: 10px">
                  <div
                    style="
                      border-bottom: 1px solid rgb(171, 171, 171);
                      padding-bottom: 20px;
                      width: 100%;
                    "
                  >
                    <b
                      style="
                        text-align: center;
                        font-weight: 600;
                        font-size: 14px;
                        color: #434343;
                      "
                      >Detail Pembayaran</b
                    >
                  </div>
                  <br />
                  <div style="background-color: #f2f4f7; padding: 10px">
                    <table style="width: 100%">
                      <tr>
                        <td
                          align="left"
                          style="font-weight: 400; font-size: 12px; color: #434343"
                        >
                          Invoice ID
                        </td>
                        <td
                          align="right"
                          style="font-weight: 600; font-size: 13px; color: #434343"
                        >
                          {$jsonData->tnos_invoice_id}
                        </td>
                      </tr>
                      <tr>
                        <td
                          align="left"
                          style="font-weight: 400; font-size: 12px; color: #434343"
                        >
                          Id Referensi
                        </td>
                        <td
                          align="right"
                          style="font-weight: 600; font-size: 13px; color: #434343"
                        >
                        {$jsonData->external_id}
                        </td>
                      </tr>
                      <tr>
                        <td
                          align="left"
                          style="font-weight: 400; font-size: 12px; color: #434343"
                        >
                          Deskripsi
                        </td>
                        <td
                          align="right"
                          style="font-weight: 600; font-size: 13px; color: #434343"
                        >
                        {$jsonData->needs}
                        </td>
                      </tr>
                      <tr>
                        <td
                          align="left"
                          style="font-weight: 400; font-size: 12px; color: #434343"
                        >
                          Pembayaran Via
                        </td>
                        <td
                          align="right"
                          style="font-weight: 600; font-size: 13px; color: #434343"
                        >
                          {$paymentChannel}
                        </td>
                      </tr>
                      <tr>
                        <td
                          align="left"
                          style="font-weight: 400; font-size: 12px; color: #434343"
                        >
                          Waktu Pembayaran
                        </td>
                        <td
                          align="right"
                          style="font-weight: 600; font-size: 13px; color: #434343"
                        >
                          {$times}
                        </td>
                      </tr>
                      <tr>
                        <td
                          align="left"
                          style="font-weight: 400; font-size: 12px; color: #434343"
                        >
                          Status Pembayaran
                        </td>
                        <td
                          align="right"
                          style="font-weight: 600; font-size: 13px; color: #434343"
                        >
                          Berhasil
                        </td>
                      </tr>
                      <tr>
                        <td
                          align="left"
                          style="font-weight: 400; font-size: 12px; color: #434343"
                        >
                          Total Pembayaran
                        </td>
                        <td
                          align="right"
                          style="font-weight: 600; font-size: 13px; color: #434343"
                        >
                          Rp {$paymentAmount}
                        </td>
                      </tr>
                    </table>
                  </div>
                </div>
              </div>
            </main>
          </body>
        </html>
        HTML;

        $data = [
            'tomail' => $customerEmail,
            'subject' => 'Pembayaran berhasil',
            'body' => $email_template,
        ];

        // $response = Http::post('https://api-dev.tnos.world/global/send/email', $data);
        $response = Http::post('https://api.tnosworld.id/global/send/email', $data);

        if ($response->successful()) {
            // Request was successful
            $responseData = $response;
            // Process the response data as needed
        } else {
            // Request failed
            $responseData = $response;
            // Handle the error response
        }



        return response()->json([
            'response' => $responseData,
            'payment' => $customerName,
        ]);
    }
}
