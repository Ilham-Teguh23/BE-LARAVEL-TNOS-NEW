<?php

use App\Http\Controllers\Api\AddDataMitraController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BadanHukumCvController;
use App\Http\Controllers\Api\BadanHukumController;
use App\Http\Controllers\Api\Callback\CreditCardsCallbackController;
use App\Http\Controllers\Api\Callback\EwalletCallbackController;
use App\Http\Controllers\Api\Callback\QrCodeCallbackController;
use App\Http\Controllers\Api\Callback\VirtualAccountCallbackController;
use App\Http\Controllers\Api\Callback\Xendit\InvoiceCallbackController;
use App\Http\Controllers\Api\CustomPrintController;
use App\Http\Controllers\Api\Dashboard\PaymentDashboardController;
use App\Http\Controllers\Api\DashboardLiveController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\FormCatatanController;
use App\Http\Controllers\Api\FortuneController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\ManualOrderController;
// use App\Http\Controllers\Api\MessageUserController;
use App\Http\Controllers\Api\Order\OrderController;
use App\Http\Controllers\Api\OrderConsultationController;
use App\Http\Controllers\Api\OrderPendampinganHukumController;
use App\Http\Controllers\Api\OrderPengamananKorporatController;
use App\Http\Controllers\Api\OrderPengamananPerorangController;
use App\Http\Controllers\Api\Payment\CreditCardPaymentController;
use App\Http\Controllers\Api\Payment\EwalletPaymentControler;
use App\Http\Controllers\Api\Payment\QrCodePaymentController;
use App\Http\Controllers\Api\Payment\VirtualAccountPaymentController;
use App\Http\Controllers\Api\PemesananController;
use App\Http\Controllers\Api\PemesananFortuneController;
// use App\Http\Controllers\Api\SecurityController;
use App\Http\Controllers\Api\StatusOrderController;
use App\Http\Controllers\api\TnosGemsProductController;
use App\Http\Controllers\Api\TnosGemsController;
use App\Http\Controllers\Api\TsaldoPaymentController;
use App\Http\Controllers\Api\VoucherController;
use App\Http\Controllers\Api\Xendit\InvoiceController;
use App\Http\Controllers\Api\Xendit\VirtualAccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Pub\Price;
use App\Http\Controllers\Api\PWARevamp\CheckoutController;
use App\Http\Controllers\Api\PWARevamp\DurasiController;
use App\Http\Controllers\Api\PWARevamp\HistoryController as PWARevampHistoryController;
use App\Http\Controllers\Api\PWARevamp\KomponenLainnyaController;
use App\Http\Controllers\Api\PWARevamp\LayananController;
use App\Http\Controllers\Api\PWARevamp\OthersController;
use App\Http\Controllers\Api\PWARevamp\ProductController;
use App\Http\Controllers\Api\PWARevamp\ProductSubSectionController;
use App\Http\Controllers\Api\PWARevamp\ProviderController;
use App\Http\Controllers\Api\PWARevamp\SectionController;
use App\Http\Controllers\Api\PWARevamp\SubSectionController;
use App\Http\Controllers\Api\PWARevamp\UnitController;
use App\Http\Controllers\OrderPass;
use App\Http\Controllers\OrderTrigger;
use Illuminate\Support\Facades\Http;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/config-cache', function () {
    Artisan::call('config:cache');
    return "Config is cache";
});
Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    return "Cache is cleared";
});
Route::get('/route-list', function () {
    //Artisan::call('route:list');
    //return "Route list";
    $r = Route::getRoutes();
    foreach ($r as $value) {
        if (str_contains($value->uri(), 'api/')) {
            echo ($value->uri() . '</br>');
        }
    };
});

//auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::post('/refresh', [AuthController::class, 'refresh']);

//reset password
Route::post('/forget-password', [ForgotPasswordController::class, 'submitForgetPasswordForm'])->name('forget.password.post');
Route::post('/reset-password', [ForgotPasswordController::class, 'submitResetPasswordForm'])->name('reset.password.post');

// Pub Pas dan trigger
Route::group(['prefix' => 'pwa', 'as' => 'pub.'], function () {
    Route::controller(Price::class)->group(function () {
        Route::get('/price', 'index');
        Route::get('/get-price', 'get_price');
        Route::get('/partnerdeka', 'partnerdeka');
    });
});


//verifikasi email user
Route::get('/auth/verify/{token}', [AuthController::class, 'verifyAccount'])->name('verify');


Route::group(['middleware' => ['jwt.verify']], function () {
});

Route::post("/custom-print", [CustomPrintController::class, "custom_print"]);
Route::post('/order-new', [OrderPass::class, 'create']);
Route::post("/order-new-history", [PWARevampHistoryController::class, "store"]);

Route::get('/order-invoice/{id}', [OrderPass::class, 'order_invoice']);

Route::post('user-profile', [AuthController::class, 'user_profile']);

// riwayay pembelian user
Route::get('/history/{id}/{type}', [HistoryController::class, 'getDataHistory']);
Route::get('/history/{id}', [HistoryController::class, 'getHistoryData']);

// konsultasi
Route::get('/konsultasi/get-detail-order/{id}', [OrderConsultationController::class, 'getDataDetailOrderById']);
Route::post('/konsultasi/in-order', [OrderConsultationController::class, 'inOrder']);
Route::post('/konsultasi/in-payment', [OrderConsultationController::class, 'inPayment']);

// pendampingan
Route::get('/pendampingan/get-detail-order/{id}', [OrderPendampinganHukumController::class, 'getDataDetailOrderById']);
Route::post('/pendampingan/in-order', [OrderPendampinganHukumController::class, 'inOrder']);
Route::post('/pendampingan/in-payment', [OrderPendampinganHukumController::class, 'inPayment']);

// pengamanan perorang
Route::get('/pengamanan-perorang/get-detail-order/{id}', [OrderPengamananPerorangController::class, 'getDataDetailOrderById']);
Route::post('/pengamanan-perorang/in-order', [OrderPengamananPerorangController::class, 'inOrder']);
Route::post('/pengamanan-perorang/in-payment', [OrderPengamananPerorangController::class, 'inPayment']);

// pengamanan korporat
Route::get('/pengamanan-korporat/get-detail-order/{id}', [OrderPengamananKorporatController::class, 'getDataDetailOrderById']);
Route::post('/pengamanan-korporat/in-order', [OrderPengamananKorporatController::class, 'inOrder']);
Route::post('/pengamanan-korporat/in-payment', [OrderPengamananKorporatController::class, 'inPayment']);

// pengamanan badan hukum
Route::get('/badan-hukum/get-detail-order/{id}', [BadanHukumController::class, 'getDataDetailOrderById']);
Route::post('/badan-hukum/in-order', [BadanHukumController::class, 'inOrder']);
Route::post('/badan-hukum/in-payment', [BadanHukumController::class, 'inPayment']);

// Order Manual
Route::prefix("manual-order")->group(function() {
    Route::prefix("in-order-manual")->group(function() {
        Route::get("/", [ManualOrderController::class, "index"]);
        Route::post("/", [ManualOrderController::class, "store"]);
        Route::get("/{id}/show", [ManualOrderController::class, "edit"]);
        Route::put("/{id}", [ManualOrderController::class, "update"]);
        Route::delete("/{id}", [ManualOrderController::class, "destroy"]);
    });
    Route::post("/upload-bukti-bayar", [ManualOrderController::class, "uploadBukti"]);
});
// End

Route::post('/tracking-status/{id}', [BadanHukumController::class, 'update_status_tracking']);
Route::post('/tracking-order-deka', [BadanHukumController::class, 'tracking_order_deka']);

// pengamanan badan hukum CV
Route::get('/badan-hukum-cv/get-detail-order/{id}', [BadanHukumCvController::class, 'getDataDetailOrderById']);
Route::post('/badan-hukum-cv/in-order', [BadanHukumCvController::class, 'inOrder']);
Route::post('/badan-hukum-cv/in-payment', [BadanHukumCvController::class, 'inPayment']);

//order
Route::get('/order', [HistoryController::class, 'getAllOrder']);
Route::get('/order/{id}', [HistoryController::class, 'getOrderById']);

//payment
Route::post('/konsultasi/payment', [OrderConsultationController::class, 'createInvoicePayment']);

Route::get('/get-all/invoice', [InvoiceController::class, 'getAllInvoice']);
Route::post('/create/invoice', [InvoiceController::class, 'createInvoice']);
Route::post('/callback/invoice', [InvoiceController::class, 'callback']);
Route::get('/user/{id}', [InvoiceController::class, 'user']);
Route::get('/order-all', [InvoiceController::class, 'user']);

Route::post('/create/virtual-account', [VirtualAccountController::class, 'createVa']);
Route::patch('/payment/virtual-account', [VirtualAccountController::class, 'VaPayment']);

// tes security
// Route::post('/security/encrypt', [SecurityController::class, 'myCrypt']);
// Route::post('/security/decrypt', [SecurityController::class, 'myDecrypt']);

// status order
Route::put('/order/status/getting-corporate-partners/{id}', [StatusOrderController::class, 'gettingCorporatePartners']);
Route::put('/order/status/go-to-location/{id}', [StatusOrderController::class, 'goToLocation']);
Route::put('/order/status/on-duty/{id}', [StatusOrderController::class, 'onDuty']);
Route::put('/order/status/document-check/{id}', [StatusOrderController::class, 'documentCheck']);
Route::put('/order/status/document-registration/{id}', [StatusOrderController::class, 'documentRegistration']);
Route::put('/order/status/registration-done/{id}', [StatusOrderController::class, 'registrationDone']);
Route::put('/order/status/document-delivery/{id}', [StatusOrderController::class, 'documentDelivery']);
Route::put('/order/status/finish/{id}', [StatusOrderController::class, 'finish']);
// Route::post('/order/status/start/{id}', [StatusOrderController::class, 'statusStart']);
// Route::post('/order/status/run/{id}', [StatusOrderController::class, 'statusRun']);

// message user
// Route::get('/user/message/{id}', [MessageUserController::class, 'fetchMessageUserById']);
// Route::get('/user/message/count-unread/{id}', [MessageUserController::class, 'countUnreadMessage']);
// Route::put('/user/message/read/{id}', [MessageUserController::class, 'readMessage']);

// add data mitra
Route::put('/mitra/update/{id}', [AddDataMitraController::class, 'addDataMitra']);

// tnos gems
// Route::get('/tnos-gems/get-detail-all-histories-point', [TsaldoPaymentController::class, 'getAllHistoriesPoint']);
// Route::get('/tnos-gems/get-payment-by-invoice-id/{id}', [TsaldoPaymentController::class, 'getPaymentByInvoiceId']);
// Route::get('/tnos-gems/fetch-all-product', [TsaldoPaymentController::class, 'fetchAllProduct']);

Route::get('/tnos-gems/fetch-order-by-id/{id}', [TsaldoPaymentController::class, 'fetchDetailOrderById']);
Route::get('/tnos-gems/fetch-point-by-user-id/{id}', [TsaldoPaymentController::class, 'fetchPointByUserId']);
Route::get('/tnos-gems/fetch-point-histories-by-user-id/{id}', [TsaldoPaymentController::class, 'getHistoriesPointByUser']);
Route::get('/tnos-gems/fetch-point-histories-by-id/{id}', [TsaldoPaymentController::class, 'getHistoriesPointById']);
Route::post('/tnos-gems/transaction', [TsaldoPaymentController::class, 'addOrder']);
Route::put('/tnos-gems/transaction/payment', [TsaldoPaymentController::class, 'inPayment']);

//voucher
Route::post('/voucher/in-voucher', [VoucherController::class, 'inVoucher']);
Route::post('/voucher/out-voucher', [VoucherController::class, 'outVoucher']);
Route::get('/voucher/checksaldo/{id}', [VoucherController::class, 'checkSaldo']);

//dashboard
Route::get('/dashboard/point/last-point-by-user/{id}', [DashboardLiveController::class, 'getLastPointByUserIdDashboard']);
Route::get('/dashboard/point/last-point/{id}', [DashboardLiveController::class, 'getLastPointByUserId']);
Route::get('/dashboard/order-voucher', [DashboardLiveController::class, 'pemesanan']);
Route::get('/dashboard/order-voucher/{id}', [DashboardLiveController::class, 'getDetailPemesananById']);
Route::get('/dashboard/payment-voucher', [DashboardLiveController::class, 'payment']);
Route::get('/dashboard/payment-voucher/{id}', [DashboardLiveController::class, 'getDetailPaymentById']);
Route::get('/dashboard/voucher/user/{id}', [DashboardLiveController::class, 'getHistoriesPointByUser']);
Route::post('/dashboard/add', [DashboardLiveController::class, 'addPoint']);

// Fortune
Route::get("/dashboard/fortune", [FortuneController::class, "getListFortune"]);
Route::post("/dashboard/fortune", [FortuneController::class, "storeFortune"]);
Route::get("/dashboard/fortune/{id_fortune}", [FortuneController::class, "showFortune"]);
Route::put("/dashboard/fortune/{id_fortune}", [FortuneController::class, "updateFortune"]);
Route::delete("/dashboard/fortune/{id_fortune}", [FortuneController::class, "deleteFortune"]);
Route::put("/dashboard/fortune/{id_fortune}/change-status", [FortuneController::class, "changeStatus"]);

// Pemesanan Fortune
Route::get("/dashboard/pemesanan_fortune", [PemesananFortuneController::class, "getListPemesananFortune"]);
Route::post("/dashboard/pemesanan_fortune", [PemesananFortuneController::class, "storeListPemesananFortune"]);
Route::get("/dashboard/pemesanan_fortune/{id_pemesanan_fortune}", [PemesananFortuneController::class, "showPemesananFortune"]);
Route::put("/dashboard/pemesanan_fortune/{id_pemesanan_fortune}", [PemesananFortuneController::class, "updatePemesananFortune"]);
Route::delete("/dashboard/pemesanan_fortune/{id_pemesanan_fortune}", [PemesananFortuneController::class, "deletePemesananFortune"]);

// Form Catatan
Route::get("/dashboard/catatan", [FormCatatanController::class, "getListCatatan"]);
Route::post("/dashboard/catatan", [FormCatatanController::class, "storeCatatan"]);
Route::get("/dashboard/catatan/{id_catatan}", [FormCatatanController::class, "showCatatan"]);
Route::delete("/dashboard/catatan/{id_catatan}", [FormCatatanController::class, "deleteCatatan"]);

//custom payment ewallet
Route::get('/payment/ewallet/fetch/{id}', [EwalletPaymentControler::class, 'getPaymentById']);
Route::post('/payment/ewallet/ovo', [EwalletPaymentControler::class, 'createEwalletOvo']);
Route::post('/payment/ewallet/dana', [EwalletPaymentControler::class, 'createEwalletDana']);
Route::post('/payment/ewallet/shopeepay', [EwalletPaymentControler::class, 'createEwalletShopeepay']);
Route::post('/payment/ewallet/linkaja', [EwalletPaymentControler::class, 'createEwalletLinkaja']);
Route::post('/payment/ewallet/astrapay', [EwalletPaymentControler::class, 'createEwalletAstrapay']);

// PWA Revamp
Route::prefix("dashboard")->group(function() {
    Route::prefix("pwa-revamp")->group(function() {
        Route::controller(ProviderController::class)->group(function() {
            Route::prefix("provider")->group(function() {
                Route::get("/", "index");
                Route::post("/", "store");
                Route::get("/{id}", "show");
                Route::put("/{id}", "update");
                Route::delete("/{id}", "destroy");
                Route::put("/{id}/change-status", "changeStatus");
            });
        });

        Route::controller(LayananController::class)->group(function() {
            Route::prefix("layanan")->group(function() {
                Route::get("/{provider_id}", "index");
                Route::post("/", "store");
                Route::get("/{idLayanan}/show", "show");
                Route::put("/{idLayanan}", "update");
                Route::delete("/{idLayanan}", "destroy");
            });
        });

        Route::controller(SectionController::class)->group(function() {
            Route::prefix("section")->group(function() {
                Route::get("/{durasi_id}", "index");
                Route::post("/", "store");
                Route::get("/{section_id}/show", "show");
                Route::put("/{id}", "update");
                Route::delete("/{id}", "destroy");
                Route::put("/{id}/change-status", "changeStatus");
            });
        });

        Route::controller(SubSectionController::class)->group(function() {
            Route::prefix("subsection")->group(function() {
                Route::get("/{section_id}", "index");
                Route::post("/", "store");
                Route::get("/{subsection}/show", "show");
                Route::put("/{id}", "update");
                Route::delete("/{id}", "destroy");
            });
        });

        Route::controller(ProductController::class)->group(function() {
            Route::prefix("product")->group(function() {
                Route::get("/{section_id}", "index");
                Route::post("/", "store");
                Route::get("/{sectionId}/show", "show");
                Route::put("/{id}", "update");
                Route::delete("/{id}", "destroy");
                Route::put("/{id}/change-status", "changeStatus");
            });
        });

        Route::controller(ProductSubSectionController::class)->group(function() {
            Route::prefix("product-sub-section")->group(function() {
                Route::get("/{section_id}", "index");
                Route::post("/", "store");
                Route::get("/{sectionId}/show", "show");
                Route::put("/{id}", "update");
                Route::delete("/{id}", "destroy");
                Route::put("/{id}/change-status", "changeStatus");
            });
        });

        Route::controller(PWARevampHistoryController::class)->group(function() {
            Route::get("/history/{id}", "index");
            Route::post("/history/{id}/{harga}", "payment");
            Route::get("/transaksi/{id}/detail", "detailTransaksi");
        });

        Route::controller(OthersController::class)->group(function() {
            Route::prefix("others")->group(function() {
                Route::get("/{is_product_id}", "index");
                Route::post("/", "store");
                Route::get("/{sectionId}/show", "show");
                Route::put("/{id}", "update");
                Route::delete("/{id}", "destroy");
                Route::put("/{id}/change-status", "changeStatus");
            });
        });

        Route::controller(CheckoutController::class)->group(function() {
            Route::prefix("/checkout")->group(function() {
                Route::get("/{layanan_id}", "index");
            });
        });

        Route::controller(UnitController::class)->group(function() {
            Route::prefix("unit")->group(function() {
                Route::get("/", "index");
                Route::post("/", "store");
                Route::get("/{id}/show", "edit");
                Route::put("/{id}", "update");
                Route::delete("/{id}", "destroy");
                Route::put("/{id}/change-status", "changeStatus");
            });
        });

        Route::controller(KomponenLainnyaController::class)->group(function() {
            Route::prefix("komponen-lainnya")->group(function() {
                Route::get("/", "index");
                Route::post("/", "store");
                Route::get("/{id}/show", "edit");
                Route::put("/{id}", "update");
                Route::delete("/{id}", "destroy");
                Route::put("/{id}/change-status", "changeStatus");
            });
        });

        Route::controller(DurasiController::class)->group(function() {
            Route::prefix("durasi")->group(function() {
                Route::get("/{layanan_id}", "index");
                Route::post("/", "store");
                Route::get("/{id}/show", "show");
                Route::put("/{id}", "update");
                Route::delete("/{id}", "destroy");
                Route::put("/{id}/change-status", "changeStatus");
            });
        });
    });
});

// payment virtual account
Route::get('/payment/va/list-bank', [VirtualAccountPaymentController::class, 'getListBank']);
Route::post('/payment/va/create', [VirtualAccountPaymentController::class, 'createCreateVirtualAccount']);

// payment credit or debit card
Route::post('/payment/cc/create', [CreditCardPaymentController::class, 'create']);
Route::post('/payment/cc/charge/create', [CreditCardPaymentController::class, 'createCharge']);
Route::put('/payment/cc/update-token', [CreditCardPaymentController::class, 'updateTokenToPaymentData']);

// payment Qr code
Route::get('/payment/qr-code/get/{id}', [QrCodePaymentController::class, 'getQrCode']);
Route::post('/payment/qr-code/create', [QrCodePaymentController::class, 'createQrCodes']);

Route::post("/test-data", [VirtualAccountCallbackController::class, "update_status_payment"]);

// custom callback
Route::post('/callback/ewallet', [EwalletCallbackController::class, 'callbackEwallet']);
Route::post('/callback/virtualaccount', [VirtualAccountCallbackController::class, 'callbackVirtualAccount']);
Route::post('/callback/credit-card/otentikasi', [CreditCardsCallbackController::class, 'otentikasiCard']);
Route::post('/callback/credit-card/tokenisasi', [CreditCardsCallbackController::class, 'tokenisasiCard']);
Route::post('/callback/qr-code', [QrCodeCallbackController::class, 'callbackQrCode']);

Route::prefix("xendit")->group(function () {
    Route::post("/callback/invoice", [InvoiceCallbackController::class, "callbackVirtualAccount"]);
});

// create order demo
Route::post('/demo/order/create', [OrderController::class, 'create']);


// payment dashboard
Route::get('/dashboard/xendit/payment', [PaymentDashboardController::class, 'getPayment']);
Route::get('/dashboard/xendit/payment/{id}', [PaymentDashboardController::class, 'getPaymentById']);

// Merubah Status

Route::put("/pemesanan/{b2b_id}", [PemesananController::class, "updateStatus"]);
Route::get("/pemesanan/{b2b_id}", [PemesananController::class, "getPesanan"]);
Route::put("/update-pembayaran", [PemesananController::class, "updatePembayaran"]);
Route::get("/no-referensi/{id}", [PemesananController::class, "getNoReferensi"]);
Route::put("/pemesanan/{b2b_id}/laporan", [PemesananController::class, "updateLaporan"]);
